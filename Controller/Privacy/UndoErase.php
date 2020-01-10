<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Config;

class UndoErase extends AbstractPrivacy implements HttpGetActionInterface //todo should be post action
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var ContextBuilder
     */
    private $actionContextBuilder;

    public function __construct(
        Context $context,
        Config $config,
        Session $customerSession,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->customerSession = $customerSession;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
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

        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => (int) $this->customerSession->getCustomerId(),
            ArgumentReader::ENTITY_TYPE => 'customer'
        ]);

        try {
            $this->action->execute($this->actionContextBuilder->create());
            $this->messageManager->addSuccessMessage(new Phrase('You canceled your account deletion.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
