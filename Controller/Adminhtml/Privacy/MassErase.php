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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;

class MassErase extends AbstractMassAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var ContextBuilder
     */
    private $actionContextBuilder;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($context, $filter, $collectionFactory);
    }

    protected function massAction(AbstractCollection $collection)
    {
        $customerErased = 0;

        foreach ($collection->getAllIds() as $customerId) {
            $this->actionContextBuilder->setParameters([
                ArgumentReader::ENTITY_ID => (int) $customerId,
                ArgumentReader::ENTITY_TYPE => 'customer'
            ]);

            try {
                $this->action->execute($this->actionContextBuilder->create());
                $customerErased++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    new Phrase('Customer with id "%1": %2', [$customerId, $e->getMessage()])
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
