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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Archive\ArchiveManager;

class MassExport extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    private ArchiveManager $archiveManager;

    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        ArchiveManager $archiveManager,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->fileFactory = $fileFactory;
        $this->archiveManager = $archiveManager;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection)
    {
        $archiveFileName = 'customers_privacy_data.zip';

        try {
            foreach ($collection->getAllIds() as $customerId) {
                $this->actionContextBuilder->setParameters([
                    ArgumentReader::ENTITY_ID => (int) $customerId,
                    ArgumentReader::ENTITY_TYPE => 'customer'
                ]);
                $result = $this->action->execute($this->actionContextBuilder->create())->getResult();
                /** @var ExportEntityInterface $exportEntity */
                $exportEntity = $result[ExportArgumentReader::EXPORT_ENTITY];
                $this->archiveManager->addToArchive($exportEntity->getFilePath(), $archiveFileName, false);
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
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index/index');
    }
}
