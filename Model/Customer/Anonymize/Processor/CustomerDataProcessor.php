<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Customer\Anonymize\AccountBlocker;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var AccountBlocker
     */
    private $accountBlocker;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        AnonymizerInterface $anonymizer,
        AccountBlocker $accountBlocker,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRegistry $customerRegistry,
        Config $config
    ) {
        $this->anonymizer = $anonymizer;
        $this->accountBlocker = $accountBlocker;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
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

            // Make sure, we don't work with cached customer data, because
            // saving cached customers may "de-anonymize" related data
            // like addresses
            $this->customerRegistry->remove($customerId);

            $this->accountBlocker->invalid($customerId);
            $this->customerRepository->save(
                $this->anonymizer->anonymize($this->customerRepository->getById($customerId))
            );
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }
}
