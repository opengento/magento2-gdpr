<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Class EraseEntity
 */
final class EraseEntity
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
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityRepositoryInterface
     */
    private $eraseEntityRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     * @param \Opengento\Gdpr\Api\EraseEntityRepositoryInterface $eraseEntityRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        Registry $registry,
        EraseEntityManagementInterface $eraseEntityManagement,
        EraseEntityRepositoryInterface $eraseEntityRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->registry = $registry;
        $this->eraseEntityManagement = $eraseEntityManagement;
        $this->eraseEntityRepository = $eraseEntityRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
    }

    /**
     * Process erase of all scheduled entities
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            $oldValue = $this->registry->registry('isSecureArea');
            $this->registry->register('isSecureArea', true, true);

            /** @var \Opengento\Gdpr\Api\Data\EraseEntityInterface $eraseEntity */
            foreach ($this->retrieveEraseEntityList()->getItems() as $eraseEntity) {
                try {
                    $this->eraseEntityManagement->process($eraseEntity);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            $this->registry->register('isSecureArea', $oldValue, true);
        }
    }

    /**
     * Retrieve erase entity scheduler list
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    private function retrieveEraseEntityList(): SearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            EraseEntityInterface::SCHEDULED_AT,
            $this->dateTime->date(),
            'lteq'
        );
        $this->searchCriteriaBuilder->addFilter(
            EraseEntityInterface::STATE,
            EraseEntityInterface::STATE_COMPLETE,
            'neq'
        );
        $this->searchCriteriaBuilder->addFilter(
            EraseEntityInterface::STATUS,
            [EraseEntityInterface::STATUS_READY, EraseEntityInterface::STATUS_FAILED],
            'in'
        );

        try {
            $eraseCustomerList = $this->eraseEntityRepository->getList($this->searchCriteriaBuilder->create());
        } catch (LocalizedException $e) {
            $eraseCustomerList = [];
        }

        return $eraseCustomerList;
    }
}
