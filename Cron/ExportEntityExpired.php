<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Delete all expired export entities
 */
class ExportEntityExpired
{
    public function __construct(
        private LoggerInterface $logger,
        private ExportEntityRepositoryInterface $exportRepository,
        private SearchCriteriaBuilder $criteriaBuilder
    ) {}

    public function execute(): void
    {
        $this->criteriaBuilder->addFilter(
            ExportEntityInterface::EXPIRED_AT,
            (new \DateTime())->format(DateTime::DATE_PHP_FORMAT),
            'lteq'
        );

        try {
            $exportList = $this->exportRepository->getList($this->criteriaBuilder->create());

            foreach ($exportList->getItems() as $exportEntity) {
                $this->exportRepository->delete($exportEntity);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}
