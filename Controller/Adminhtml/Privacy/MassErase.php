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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;

class MassErase extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        private EraseEntityManagementInterface $eraseEntityManagement,
        private EraseEntityRepositoryInterface $eraseEntityRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection): ResultInterface|ResponseInterface
    {
        $customerErased = 0;

        $this->searchCriteriaBuilder->addFilter(EraseEntityInterface::ENTITY_ID, $collection->getAllIds(), 'in');
        $this->searchCriteriaBuilder->addFilter(EraseEntityInterface::ENTITY_TYPE, 'customer');
        $eraseEntityList = $this->eraseEntityRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($eraseEntityList->getItems() as $eraseEntity) {
            try {
                $this->eraseEntityManagement->process($eraseEntity);
                $customerErased++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    new Phrase('Customer with id "%1": %2', [$eraseEntity->getEntityId(), $e->getMessage()])
                );
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
            }
        }

        if ($customerErased) {
            $this->messageManager->addSuccessMessage(
                new Phrase('A total of %1 record(s) were erased.', [$customerErased])
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index/index');
    }
}
