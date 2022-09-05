<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Exception;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Config;

class ErasePost extends AbstractPrivacy implements HttpPostActionInterface
{
    /**
     * @var AuthenticationInterface
     */
    private AuthenticationInterface $authentication;

    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        Session $customerSession,
        Http $response,
        AuthenticationInterface $authentication,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->authentication = $authentication;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($request, $resultFactory, $messageManager, $config, $customerSession, $response);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    protected function executeAction(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/privacy/settings');

        $customerId = (int) $this->customerSession->getCustomerId();
        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => $customerId,
            ArgumentReader::ENTITY_TYPE => 'customer'
        ]);

        try {
            $this->authentication->authenticate($customerId, $this->request->getParam('password'));
            $this->action->execute($this->actionContextBuilder->create());
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed soon.'));
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setRefererOrBaseUrl();
        } catch (UserLockedException $e) {
            $this->customerSession->logout();
            try {
                $this->customerSession->start();
            } catch (SessionException $e) {
                $this->messageManager->addExceptionMessage($e, new Phrase('The session initialization has failed.'));
            }
            $this->messageManager->addErrorMessage(
                new Phrase('You did not sign in correctly or your account is temporarily disabled.')
            );
            $resultRedirect->setPath('customer/account/login');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
