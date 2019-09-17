<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Config;

/**
 * Action Download Export
 */
class Download extends AbstractPrivacy
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
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param Config $config
     * @param FileFactory $fileFactory
     * @param MoveToArchive $moveToArchive
     * @param ExportEntityRepositoryInterface $exportRepository
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityRepositoryInterface $exportRepository,
        Session $customerSession
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportRepository = $exportRepository;
        $this->customerSession = $customerSession;
        parent::__construct($context, $config);
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
            return $this->download();
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
        $customerId = (int)$this->customerSession->getCustomerId();
        $export = $this->exportRepository->getByEntity($customerId, 'customer');
        $archiveFileName = 'customer_privacy_data_' . $customerId . '.zip';

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
