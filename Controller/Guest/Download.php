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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Config;

/**
 * Class Download Export
 */
class Download extends AbstractGuest
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Opengento\Gdpr\Model\Archive\MoveToArchive
     */
    private $moveToArchive;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    /**
     * @param Context $context
     * @param Config $config
     * @param FileFactory $fileFactory
     * @param MoveToArchive $moveToArchive
     * @param ExportEntityRepositoryInterface $exportRepository
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityRepositoryInterface $exportRepository,
        OrderLoaderInterface $orderLoader,
        Registry $registry
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportRepository = $exportRepository;
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
            $this->download();
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                new Phrase('The document does not exists and may have expired. Please renew your demand.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * Download the export document
     *
     * @return ResponseInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Exception
     */
    private function download(): ResponseInterface
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->registry->registry('current_order');
        $export = $this->exportRepository->getByEntity($this->retrieveOrderId(), 'order');
        $archiveFileName = 'customer_privacy_data_' . $order->getCustomerLastname() . '.zip';

        return $this->fileFactory->create(
            $archiveFileName,
            [
                'type' => 'filename',
                'value' => $this->moveToArchive->prepareArchive($export->getFilePath(), $archiveFileName),
                'rm' => true,
            ],
            DirectoryList::TMP
        );
    }
}
