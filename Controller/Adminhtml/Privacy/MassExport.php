<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Archive\ArchiveManager;

class MassExport extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        private FileFactory $fileFactory,
        private ArchiveManager $archiveManager,
        private ExportEntityManagementInterface $exportEntityManagement,
        private ExportEntityRepositoryInterface $exportEntityRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection): ResultInterface|ResponseInterface
    {
        $archiveFileName = 'customers_privacy_data.zip';

        $this->searchCriteriaBuilder->addFilter(ExportEntityInterface::ENTITY_ID, $collection->getAllIds(), 'in');
        $this->searchCriteriaBuilder->addFilter(ExportEntityInterface::ENTITY_TYPE, 'customer');
        $exportEntityList = $this->exportEntityRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($exportEntityList->getItems() as $exportEntity) {
            try {
                $this->archiveManager->addToArchive(
                    $this->exportEntityManagement->export($exportEntity)->getFilePath(),
                    $archiveFileName,
                    false
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    new Phrase('Customer with id "%1": %2', [$exportEntity->getEntityId(), $e->getMessage()])
                );
            }
        }

        try {
            return $this->fileFactory->create(
                $archiveFileName,
                [
                    'type' => 'filename',
                    'value' => $archiveFileName,
                    'rm' => true,
                ],
                DirectoryList::TMP
            );
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index/index');
    }
}
