<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Anonymize\AccountBlocker;
use Opengento\Gdpr\Service\Anonymize\AnonymizeTool;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class CustomerDataProcessor
 */
final class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizeTool
     */
    private $anonymizeTool;

    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AccountBlocker
     */
    private $accountBlocker;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizeTool $anonymizeTool
     * @param \Opengento\Gdpr\Service\Anonymize\AccountBlocker $accountBlocker
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        AnonymizeTool $anonymizeTool,
        AccountBlocker $accountBlocker,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->anonymizeTool = $anonymizeTool;
        $this->accountBlocker = $accountBlocker;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        try {
            if ($this->config->isCustomerRemovedNoOrders()) {
                $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
                $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

                if (!$orderList->getTotalCount()) {
                    $this->customerRepository->deleteById($customerId);

                    return true;
                }
            }

            $customer = $this->customerRepository->getById($customerId);
            $customer->setFirstname($this->anonymizeTool->anonymousValue());
            $customer->setMiddlename($this->anonymizeTool->anonymousValue());
            $customer->setLastname($this->anonymizeTool->anonymousValue());
            $customer->setEmail(
                $this->anonymizeTool->anonymousEmail((string) $customer->getStoreId(), (string) $customerId)
            );
            $customer->setTaxvat(null);

            $this->accountBlocker->invalid($customerId);

            $this->customerRepository->save($customer);
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
