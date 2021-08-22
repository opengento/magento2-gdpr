<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use DateTime;
use Magento\Customer\Api\CustomerRepositoryInterface;
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
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use function mt_rand;
use function sha1;
use function uniqid;
use const PHP_INT_MAX;

final class CustomerDataProcessor implements ProcessorInterface
{
    private const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';

    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

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
     * @var SessionCleanerInterface
     */
    private $sessionCleaner;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        AnonymizerInterface $anonymizer,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        CustomerRegistry $customerRegistry,
        SessionCleanerInterface $sessionCleaner,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->anonymizer = $anonymizer;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->sessionCleaner = $sessionCleaner;
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
            if ($this->shouldRemoveCustomerWithoutOrders() && !$this->fetchOrdersList($customerId)->getTotalCount()) {
                $isRemoved = $this->customerRepository->deleteById($customerId);
            }
            if (!$isRemoved) {
                $this->anonymizeCustomer($customerId);
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $this->sessionCleaner->clearFor($customerId);

        return true;
    }

    private function fetchOrdersList(int $customerId): OrderSearchResultInterface
    {
        $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);

        return $this->orderRepository->getList($this->criteriaBuilder->create());
    }

    /**
     * @param int $customerId
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws InputMismatchException
     */
    private function anonymizeCustomer(int $customerId): void
    {
        $this->customerRegistry->remove($customerId);

        $secureData = $this->customerRegistry->retrieveSecureData($customerId);
        $dateTime = (new DateTime())->setTimestamp(PHP_INT_MAX);
        $secureData->setData('lock_expires', $dateTime->format(DateTimeFormat::DATETIME_PHP_FORMAT));
        $secureData->setPasswordHash(sha1(uniqid((string) mt_rand(), true)));

        $this->customerRepository->save(
            $this->anonymizer->anonymize($this->customerRepository->getById($customerId))
        );
    }

    private function shouldRemoveCustomerWithoutOrders(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ERASURE_REMOVE_CUSTOMER, ScopeInterface::SCOPE_STORE);
    }
}
