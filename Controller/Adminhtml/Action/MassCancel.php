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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\Collection;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\CollectionFactory;

class MassCancel extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_cancel';

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
        try {
            $canceled = $this->massAction();
            $this->messageManager->addSuccessMessage(
                new Phrase('A total of %1 record(s) have been canceled.', [$canceled])
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    private function massAction(): int
    {
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $canceled = 0;

        /** @var ActionEntityInterface $item */
        foreach ($collection->getItems() as $item) {
            if ($item->getState() === ActionEntityInterface::STATE_PENDING) {
                $item->setState(ActionEntityInterface::STATE_CANCELED);
                $this->actionEntityRepository->save($item);
                $canceled++;
            }
        }

        return $canceled;
    }
}
