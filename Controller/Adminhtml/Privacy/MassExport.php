<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Model\Export\ExportEntityData;

class MassExport extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var MoveToArchive
     */
    private $moveToArchive;

    /**
     * @var ExportEntityData
     */
    private $exportEntityData;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        MoveToArchive $moveToArchive,
        ExportEntityData $exportEntityData
    ) {
        $this->fileFactory = $fileFactory;
        $this->moveToArchive = $moveToArchive;
        $this->exportEntityData = $exportEntityData;
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection)
    {
        $archiveFileName = 'customers_privacy_data.zip';

        try {
            foreach ($collection->getAllIds() as $customerId) {
                $this->moveToArchive->prepareArchive(
                    $this->exportEntityData->export((int) $customerId, 'customer'),
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

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index/index');
    }
}
