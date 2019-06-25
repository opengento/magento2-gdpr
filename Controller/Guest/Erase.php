<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\EraseEntityType;

/**
 * Class Erase
 */
class Erase extends AbstractGuest
{
    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Config $config,
        OrderLoaderInterface $orderLoader,
        EraseEntityManagementInterface $eraseEntityManagement,
        Registry $registry
    ) {
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($context, $config, $orderLoader, $registry);
    }

    /**
     * @inheritdoc
     */
    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererOrBaseUrl();

        try {
            $this->eraseEntityManagement->create($this->retrieveOrder()->getEntityId(), 'order');
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
