<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use DateTime;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Model\Customer\OrigDataRegistry;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use function mt_rand;
use function sha1;
use function uniqid;
use const PHP_INT_MAX;

final class CustomerDataProcessor implements ProcessorInterface
{
    private const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';

    private AnonymizerInterface $anonymizer;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    private OrderRepositoryInterface $orderRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    /**
     * @var CustomerRegistry
     */
    private CustomerRegistry $customerRegistry;

    /**
     * @var OrigDataRegistry
     */
    private OrigDataRegistry $origDataRegistry;

    /**
     * @var SessionCleanerInterface
     */
    private SessionCleanerInterface $sessionCleaner;

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        AnonymizerInterface $anonymizer,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        CustomerRegistry $customerRegistry,
        OrigDataRegistry $origDataRegistry,
        SessionCleanerInterface $sessionCleaner,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->anonymizer = $anonymizer;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->origDataRegistry = $origDataRegistry;
        $this->sessionCleaner = $sessionCleaner;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(int $customerId): bool
    {
        try {
            $this->processCustomerData($customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $this->sessionCleaner->clearFor($customerId);

        return true;
    }

    /**
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function processCustomerData(int $customerId): void
    {
        $customer = $this->customerRepository->getById($customerId);
        $this->origDataRegistry->set(clone $customer);

        $isRemoved = false;
        if ($this->shouldRemoveCustomerWithoutOrders() && !$this->fetchOrdersList($customer)->getTotalCount()) {
            $isRemoved = $this->customerRepository->deleteById($customer->getId());
        }
        if (!$isRemoved) {
            $this->anonymizeCustomer($customer);
        }
    }

    private function fetchOrdersList(CustomerInterface $customer): OrderSearchResultInterface
    {
        $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customer->getId());

        return $this->orderRepository->getList($this->criteriaBuilder->create());
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws InputMismatchException
     */
    private function anonymizeCustomer(CustomerInterface $customer): void
    {
        $this->customerRegistry->remove($customer->getId());

        $secureData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $dateTime = (new DateTime())->setTimestamp(PHP_INT_MAX);
        $secureData->setData('lock_expires', $dateTime->format(DateTimeFormat::DATETIME_PHP_FORMAT));
        $secureData->setPasswordHash(sha1(uniqid((string) mt_rand(), true)));

        $this->customerRepository->save($this->anonymizer->anonymize($customer));
    }

    private function shouldRemoveCustomerWithoutOrders(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ERASURE_REMOVE_CUSTOMER, ScopeInterface::SCOPE_STORE);
    }
}
