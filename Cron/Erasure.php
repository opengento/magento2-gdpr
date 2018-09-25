<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

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
final class Erasure
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
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseCustomerManagementInterface $eraseCustomerManagement
     * @param \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface $eraseCustomerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        Registry $registry,
        EraseCustomerManagementInterface $eraseCustomerManagement,
        EraseCustomerRepositoryInterface $eraseCustomerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->registry = $registry;
        $this->eraseCustomerManagement = $eraseCustomerManagement;
        $this->eraseCustomerRepository = $eraseCustomerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        $this->searchCriteriaBuilder->addFilter(EraseCustomerInterface::SCHEDULED_AT, new \DateTime(), 'lteq');
        $this->searchCriteriaBuilder->addFilter(
            EraseCustomerInterface::STATE,
            EraseCustomerInterface::STATE_COMPLETE,
            'neq'
        );
        $this->searchCriteriaBuilder->addFilter(
            EraseCustomerInterface::STATUS,
            [EraseCustomerInterface::STATUS_READY, EraseCustomerInterface::STATUS_FAILED],
            'in'
        );

        try {
            $eraseCustomerList = $this->eraseCustomerRepository->getList($this->searchCriteriaBuilder->create());
        } catch (LocalizedException $e) {
            $eraseCustomerList = [];
        }

        return $eraseCustomerList;
    }
}
