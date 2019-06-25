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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

/**
 * Class ErasePost
 */
class ErasePost extends AbstractPrivacy
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    private $authentication;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Model\Session $session
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     */
    public function __construct(
        Context $context,
        Config $config,
        Validator $formKeyValidator,
        AuthenticationInterface $authentication,
        Session $session,
        EraseEntityManagementInterface $eraseEntityManagement
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->authentication = $authentication;
        $this->session = $session;
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($context, $config);
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/privacy/settings');

        if (!$this->getRequest()->getParams() || !$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setRefererOrBaseUrl();
        }

        try {
            $customerId = (int) $this->session->getCustomerId();
            $this->authentication->authenticate($customerId, $this->getRequest()->getParam('password'));
            $this->eraseEntityManagement->create($customerId, 'customer');
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed.'));
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
