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
use Magento\Framework\DataObject;
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
use Random\RandomException;

use function random_int;
use function sha1;
use function uniqid;

use const PHP_INT_MAX;

class CustomerDataProcessor implements ProcessorInterface
{
    private const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';

    public function __construct(
        private AnonymizerInterface $anonymizer,
        private CustomerRepositoryInterface $customerRepository,
        private OrderRepositoryInterface $orderRepository,
        private SearchCriteriaBuilder $criteriaBuilder,
        private CustomerRegistry $customerRegistry,
        private OrigDataRegistry $origDataRegistry,
        private SessionCleanerInterface $sessionCleaner,
        private ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * @throws LocalizedException
     * @throws RandomException
     */
    public function execute(int $entityId): bool
    {
        try {
            $this->processCustomerData($entityId);
        } catch (NoSuchEntityException) {
            return false;
        }

        $this->sessionCleaner->clearFor($entityId);

        return true;
    }

    /**
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws RandomException
     */
    private function processCustomerData(int $customerId): void
    {
        $customer = $this->customerRepository->getById($customerId);
        $this->origDataRegistry->set(clone $customer);

        $isRemoved = false;
        if ($this->shouldRemoveCustomerWithoutOrders($customer) && !$this->fetchOrdersList($customer)->getTotalCount()) {
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
     * @throws RandomException
     */
    private function anonymizeCustomer(CustomerInterface $customer): void
    {
        $this->customerRegistry->remove($customer->getId());

        $secureData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $dateTime = (new DateTime())->setTimestamp(PHP_INT_MAX);
        $secureData->setData('lock_expires', $dateTime->format(DateTimeFormat::DATETIME_PHP_FORMAT));
        $secureData->setPasswordHash(sha1(uniqid((string)random_int(), true)));

        $customer = $this->anonymizer->anonymize($customer);
        if ($customer instanceof DataObject) {
            $customer->setData('ignore_validation_flag', true);
        }

        $this->customerRepository->save($customer);
    }

    private function shouldRemoveCustomerWithoutOrders(CustomerInterface $customer): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_REMOVE_CUSTOMER,
            ScopeInterface::SCOPE_WEBSITE,
            $customer->getWebsiteId()
        );
    }
}
