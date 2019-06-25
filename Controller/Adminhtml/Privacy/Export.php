<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportInterface;
use Opengento\Gdpr\Model\Archive\MoveToArchive;

/**
 * Class Export
 */
class Export extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Opengento\Gdpr\Model\Archive\MoveToArchive
     */
    private $moveToArchive;

    /**
     * @var \Opengento\Gdpr\Api\ExportInterface
     */
    private $exportManagement;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Opengento\Gdpr\Model\Archive\MoveToArchive $moveToArchive
     * @param \Opengento\Gdpr\Api\ExportInterface $exportManagement
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportInterface $exportManagement
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportManagement = $exportManagement;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $customerId = (int) $this->getRequest()->getParam('id');
            $fileName = $this->exportManagement->exportToFile($customerId, 'personal_data', 'customer');
            $archiveFileName = 'customer_privacy_data_' . $customerId . '.zip';

            return $this->fileFactory->create(
                $archiveFileName,
                [
                    'type' => 'filename',
                    'value' => $this->moveToArchive->prepareArchive($fileName, $archiveFileName),
                    'rm' => true,
                ],
                DirectoryList::TMP
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
