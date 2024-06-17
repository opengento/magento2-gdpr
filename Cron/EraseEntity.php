<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Process erase of all scheduled entities
 */
class EraseEntity
{
    private LoggerInterface $logger;

    private Config $config;

    private Registry $registry;

    private EraseEntityManagementInterface $eraseManagement;

    private EraseEntityRepositoryInterface $eraseRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    public function __construct(
        LoggerInterface $logger,
        Config $config,
        Registry $registry,
        EraseEntityManagementInterface $eraseManagement,
        EraseEntityRepositoryInterface $eraseRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        DateTime $dateTime
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->registry = $registry;
        $this->eraseManagement = $eraseManagement;
        $this->eraseRepository = $eraseRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->dateTime = $dateTime;
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            $oldValue = $this->registry->registry('isSecureArea');
            $this->registry->register('isSecureArea', true, true);

            foreach ($this->retrieveEraseEntityList()->getItems() as $eraseEntity) {
                try {
                    $this->eraseManagement->process($eraseEntity);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage(), $e->getTrace());
                }
            }

            $this->registry->register('isSecureArea', $oldValue, true);
        }
    }

    /**
     * @return EraseEntitySearchResultsInterface
     */
    private function retrieveEraseEntityList(): SearchResultsInterface
    {
        $this->criteriaBuilder->addFilter(
            EraseEntityInterface::SCHEDULED_AT,
            $this->dateTime->date(),
            'lteq'
        );
        $this->criteriaBuilder->addFilter(
            EraseEntityInterface::STATE,
            EraseEntityInterface::STATE_COMPLETE,
            'neq'
        );
        $this->criteriaBuilder->addFilter(
            EraseEntityInterface::STATUS,
            [EraseEntityInterface::STATUS_READY, EraseEntityInterface::STATUS_FAILED],
            'in'
        );

        try {
            $eraseCustomerList = $this->eraseRepository->getList($this->criteriaBuilder->create());
        } catch (LocalizedException $e) {
            $eraseCustomerList = [];
        }

        return $eraseCustomerList;
    }
}
