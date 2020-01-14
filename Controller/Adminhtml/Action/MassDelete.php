<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Action;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_delete';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ActionEntityRepositoryInterface $actionEntityRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->actionEntityRepository = $actionEntityRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        try {
            $this->massAction($this->filter->getCollection($this->collectionFactory->create()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @return void
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    private function massAction(AbstractDb $collection): void
    {
        $count = $collection->count();

        /** @var ActionInterface $actionEntity */
        foreach ($collection->getItems() as $actionEntity) {
            $this->actionEntityRepository->delete($actionEntity);
        }

        $this->messageManager->addSuccessMessage(new Phrase('A total of %1 record(s) have been deleted.', [$count]));
    }
}
