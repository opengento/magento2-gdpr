<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Export all scheduled entities
 */
final class ExportEntity
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
     * @var ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityManagementInterface $exportManagement,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->exportRepository = $exportRepository;
        $this->exportManagement = $exportManagement;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isExportEnabled()) {
            $this->criteriaBuilder->addFilter(ExportEntityInterface::EXPORTED_AT, true, 'null');
            $this->criteriaBuilder->addFilter(ExportEntityInterface::FILE_PATH, true, 'null');

            try {
                $exportList = $this->exportRepository->getList($this->criteriaBuilder->create());

                foreach ($exportList->getItems() as $exportEntity) {
                    $this->exportManagement->export($exportEntity);
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }
}
