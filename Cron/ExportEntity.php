<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

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
    private $exportEntityRepository;

    /**
     * @var ExportEntityManagementInterface
     */
    private $exportEntityManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ExportEntityRepositoryInterface $exportEntityRepository,
        ExportEntityManagementInterface $exportEntityManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->exportEntityRepository = $exportEntityRepository;
        $this->exportEntityManagement = $exportEntityManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Export all scheduled entities
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isExportEnabled()) {
            try {
                $this->searchCriteriaBuilder->addFilter(ExportEntityInterface::EXPORTED_AT, true, 'null');
                $this->searchCriteriaBuilder->addFilter(ExportEntityInterface::FILE_PATH, true, 'null');
                $exportList = $this->exportEntityRepository->getList($this->searchCriteriaBuilder->create());

                /** @var ExportEntityInterface $exportEntity */
                foreach ($exportList->getItems() as $exportEntity) {
                    $this->exportEntityManagement->export($exportEntity);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }
}
