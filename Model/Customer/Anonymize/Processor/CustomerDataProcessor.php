<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
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
     * @var Config
     */
    private $config;

    public function __construct(
        AnonymizerInterface $anonymizer,
        AccountBlocker $accountBlocker,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->anonymizer = $anonymizer;
        $this->accountBlocker = $accountBlocker;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
