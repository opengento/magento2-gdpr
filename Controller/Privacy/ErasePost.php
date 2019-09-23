<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

class ErasePost extends AbstractPrivacy
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EraseEntityManagementInterface
     */
    private $eraseManagement;

    public function __construct(
        Context $context,
        Config $config,
        Validator $formKeyValidator,
        AuthenticationInterface $authentication,
        Session $session,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->authentication = $authentication;
        $this->session = $session;
        $this->eraseManagement = $eraseManagement;
        parent::__construct($context, $config);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    protected function executeAction()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/privacy/settings');

        if (!$this->getRequest()->getParams() || !$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setRefererOrBaseUrl();
        }

        try {
            $customerId = (int) $this->session->getCustomerId();
            $this->authentication->authenticate($customerId, $this->getRequest()->getParam('password'));
            $this->eraseManagement->create($customerId, 'customer');
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed soon.'));
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setRefererOrBaseUrl();
        } catch (UserLockedException $e) {
            $this->session->logout();
            $this->session->start();
            $this->messageManager->addErrorMessage(
                new Phrase('You did not sign in correctly or your account is temporarily disabled.')
            );
            $resultRedirect->setPath('customer/account/login');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
