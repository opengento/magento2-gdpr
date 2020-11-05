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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Model\Customer\Anonymize\AccountBlocker;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class CustomerDataProcessor implements ProcessorInterface
{
    private const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';

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
    private $criteriaBuilder;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        AnonymizerInterface $anonymizer,
        AccountBlocker $accountBlocker,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        CustomerRegistry $customerRegistry,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->anonymizer = $anonymizer;
        $this->accountBlocker = $accountBlocker;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(int $customerId): bool
    {
        $isRemoved = false;
        try {
            if ($this->shouldRemoveCustomerWithoutOrders()) {
                $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
                $orderList = $this->orderRepository->getList($this->criteriaBuilder->create());

                if (!$orderList->getTotalCount()) {
                    $isRemoved = $this->customerRepository->deleteById($customerId);
                }
            }

            // Make sure, we don't work with cached customer data, because
            // saving cached customers may "de-anonymize" related data
            // like addresses
            $this->customerRegistry->remove($customerId);

            if (!$isRemoved) {
                $this->accountBlocker->invalid($customerId);
                $this->customerRepository->save(
                    $this->anonymizer->anonymize($this->customerRepository->getById($customerId))
                );
            }

        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    private function shouldRemoveCustomerWithoutOrders(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_REMOVE_CUSTOMER,
            ScopeInterface::SCOPE_STORE
        );
    }
}
