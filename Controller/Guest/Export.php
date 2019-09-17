<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Config;

/**
 * Class Export
 */
class Export extends AbstractGuest
{
    /**
     * @var \Opengento\Gdpr\Api\ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @param Context $context
     * @param Config $config
     * @param ExportEntityManagementInterface $exportManagement
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Config $config,
        ExportEntityManagementInterface $exportManagement,
        OrderLoaderInterface $orderLoader,
        Registry $registry
    ) {
        $this->exportManagement = $exportManagement;
        parent::__construct($context, $config, $orderLoader, $registry);
    }

    /**
     * @inheritdoc
     */
    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isExportEnabled();
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        try {
            $this->exportManagement->create($this->retrieveOrderId(), 'order');
            $this->messageManager->addSuccessMessage(new Phrase('You will be notified when the export is ready.'));
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addNoticeMessage(new Phrase('A document is already available in your order page.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
