<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Config;

class Download extends AbstractGuest
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        ExportEntityRepositoryInterface $exportRepository,
        OrderLoaderInterface $orderLoader,
        Registry $registry
    ) {
        $this->fileFactory = $fileFactory;
        $this->exportRepository = $exportRepository;
        parent::__construct($context, $config, $orderLoader, $registry);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isExportEnabled();
    }

    protected function executeAction()
    {
        try {
            /** @var OrderInterface $order */
            $order = $this->registry->registry('current_order');

            return $this->fileFactory->create(
                'customer_privacy_data_' . $order->getCustomerLastname() . '.zip',
                [
                    'type' => 'filename',
                    'value' => $this->exportRepository->getByEntity((int) $order->getEntityId(), 'order')->getFilePath(),
                    'rm' => true,
                ],
                DirectoryList::TMP
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                new Phrase('The document does not exists and may have expired. Please renew your demand.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
