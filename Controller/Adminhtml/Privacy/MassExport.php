<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Export\ExportEntityFactory;

/**
 * Class MassExport
 */
class MassExport extends AbstractMassAction
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
     * @var \Opengento\Gdpr\Api\ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @var \Opengento\Gdpr\Model\Export\ExportEntityFactory
     */
    private $exportEntityFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Opengento\Gdpr\Model\Archive\MoveToArchive $moveToArchive
     * @param \Opengento\Gdpr\Api\ExportEntityManagementInterface $exportManagement
     * @param \Opengento\Gdpr\Model\Export\ExportEntityFactory $exportEntityFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityManagementInterface $exportManagement,
        ExportEntityFactory $exportEntityFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportManagement = $exportManagement;
        $this->exportEntityFactory = $exportEntityFactory;
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @inheritdoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $archiveFileName = 'customers_privacy_data.zip';

        try {
            foreach ($collection->getAllIds() as $customerId) {
                $this->moveToArchive->prepareArchive(
                    $this->moveToArchive->prepareArchive(
                        $this->exportManagement->export($this->exportEntityFactory->create((int) $customerId)),
                        'customer_privacy_data_' . $customerId . '.zip'
                    ),
                    $archiveFileName
                );
            }

            return $this->fileFactory->create(
                $archiveFileName,
                [
                    'type' => 'filename',
                    'value' => $archiveFileName,
                    'rm' => true,
                ],
                DirectoryList::TMP
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                new Phrase('Customer with id "%1": %2', [$customerId ?? 'N/A', $e->getMessage()])
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index/index');
    }
}
