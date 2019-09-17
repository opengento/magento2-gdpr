<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Guest;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Controller\Adminhtml\AbstractAction;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Export\ExportEntityData;

/**
 * Class Export
 */
class Export extends AbstractAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_export';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Opengento\Gdpr\Model\Archive\MoveToArchive
     */
    private $moveToArchive;

    /**
     * @var ExportEntityData
     */
    private $exportEntityData;

    /**
     * @param Context $context
     * @param Config $config
     * @param FileFactory $fileFactory
     * @param MoveToArchive $moveToArchive
     * @param ExportEntityData $exportEntityData
     */
    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityData $exportEntityData
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportEntityData = $exportEntityData;
        parent::__construct($context, $config);
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        try {
            $entityId = (int) $this->getRequest()->getParam('id');
            $fileName = $this->exportEntityData->export($entityId, 'order');
            $archiveFileName = 'guest_privacy_data_' . $entityId . '.zip';

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
