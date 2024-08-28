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
use Psr\Log\LoggerInterface;

/**
 * Process erase of all scheduled entities
 */
class EraseEntity
{
    public function __construct(
        private LoggerInterface $logger,
        private Registry $registry,
        private EraseEntityManagementInterface $eraseManagement,
        private EraseEntityRepositoryInterface $eraseRepository,
        private SearchCriteriaBuilder $criteriaBuilder,
        private DateTime $dateTime
    ) {}

    public function execute(): void
    {
        $oldValue = $this->registry->registry('isSecureArea');
        $this->registry->register('isSecureArea', true, true);

        try {
            foreach ($this->retrieveEraseEntityList()->getItems() as $eraseEntity) {
                try {
                    $this->eraseManagement->process($eraseEntity);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage(), ['exception' => $e]);
                }
            }
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage(), ['exception' => $e]);
        }

        $this->registry->register('isSecureArea', $oldValue, true);
    }

    /**
     * @return EraseEntitySearchResultsInterface
     * @throws LocalizedException
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

        return $this->eraseRepository->getList($this->criteriaBuilder->create());
    }
}
