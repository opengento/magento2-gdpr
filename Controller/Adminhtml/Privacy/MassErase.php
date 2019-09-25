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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;

class MassErase extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    /**
     * @var EraseEntityManagementInterface
     */
    private $eraseManagement;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->eraseManagement = $eraseManagement;
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection)
    {
        $customerErased = 0;

        foreach ($collection->getAllIds() as $customerId) {
            try {
                $this->eraseManagement->process($this->eraseManagement->create((int) $customerId, 'customer'));
                $customerErased++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    new Phrase('Customer with id "%1": %2', [$customerId, $e->getMessage()])
                );
            } catch (\Exception $e) {
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
