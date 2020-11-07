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
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Delete all expired export entities
 */
final class ExportEntityExpired
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ExportEntityRepositoryInterface $exportRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->exportRepository = $exportRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isExportEnabled()) {
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
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }
}
