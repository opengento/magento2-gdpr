<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Api\EraseCustomerManagementInterface;
use Opengento\Gdpr\Api\EraseCustomerRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized
 */
class Erasure
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerManagementInterface
     */
    private $eraseCustomerManagement;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface
     */
    private $eraseCustomerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseCustomerManagementInterface $eraseCustomerManagement
     * @param \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface $eraseCustomerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        Registry $registry,
        EraseCustomerManagementInterface $eraseCustomerManagement,
        EraseCustomerRepositoryInterface $eraseCustomerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->registry = $registry;
        $this->eraseCustomerManagement = $eraseCustomerManagement;
        $this->eraseCustomerRepository = $eraseCustomerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Process all scheduled erase customer
     *
     * @return void
     */
    public function execute()
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            $oldValue = $this->registry->registry('isSecureArea');
            $this->registry->register('isSecureArea', true, true);

            /** @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface $eraseCustomer */
            foreach ($this->retrieveEraseCustomerList()->getItems() as $eraseCustomer) {
                try {
                    $this->eraseCustomerManagement->process($eraseCustomer);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            $this->registry->register('isSecureArea', $oldValue, true);
        }
    }

    /**
     * Retrieve erase customer scheduler list
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    private function retrieveEraseCustomerList(): SearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilters([
            $this->createFilter(EraseCustomerInterface::SCHEDULED_AT, new \DateTime(), 'lteq'),
            $this->createFilter(EraseCustomerInterface::STATE, EraseCustomerInterface::STATE_PENDING),
            $this->createFilter(EraseCustomerInterface::STATUS, EraseCustomerInterface::STATUS_READY),
        ]);
        $this->searchCriteriaBuilder->addFilters([
            $this->createFilter(EraseCustomerInterface::SCHEDULED_AT, new \DateTime(), 'lteq'),
            $this->createFilter(EraseCustomerInterface::STATE, EraseCustomerInterface::STATE_PROCESSING),
            $this->createFilter(EraseCustomerInterface::STATUS, EraseCustomerInterface::STATUS_FAILED),
        ]);

        try {
            $eraseCustomerList = $this->eraseCustomerRepository->getList($this->searchCriteriaBuilder->create());
        } catch (LocalizedException $e) {
            $eraseCustomerList = [];
        }

        return $eraseCustomerList;
    }

    /**
     * Create a new search criteria filter
     *
     * @param string $field
     * @param array|string $value
     * @param string $conditionType
     * @return \Magento\Framework\Api\Filter
     */
    private function createFilter(string $field, $value, string $conditionType = 'eq'): Filter
    {
        $this->filterBuilder->setField($field);
        $this->filterBuilder->setValue($value);
        $this->filterBuilder->setConditionType($conditionType);

        return $this->filterBuilder->create();
    }
}
