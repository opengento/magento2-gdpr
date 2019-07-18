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
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Controller\Adminhtml\AbstractAction;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Export\ExportEntityFactory;

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
     * @var \Opengento\Gdpr\Api\ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @var \Opengento\Gdpr\Model\Export\ExportEntityFactory
     */
    private $exportEntityFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Opengento\Gdpr\Model\Archive\MoveToArchive $moveToArchive
     * @param \Opengento\Gdpr\Api\ExportEntityManagementInterface $exportManagement
     * @param \Opengento\Gdpr\Model\Export\ExportEntityFactory $exportEntityFactory
     */
    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityManagementInterface $exportManagement,
        ExportEntityFactory $exportEntityFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportManagement = $exportManagement;
        $this->exportEntityFactory = $exportEntityFactory;
        parent::__construct($context, $config);
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        try {
            $entityId = (int) $this->getRequest()->getParam('id');
            $fileName = $this->exportManagement->export($this->exportEntityFactory->create($entityId));
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
