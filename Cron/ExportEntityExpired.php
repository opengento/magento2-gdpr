<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Class ExportEntityExpired
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
    private $exportEntityRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param ExportEntityRepositoryInterface $exportEntityRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ExportEntityRepositoryInterface $exportEntityRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->exportEntityRepository = $exportEntityRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Delete all expired export entities
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isExportEnabled()) {
            try {
                $this->searchCriteriaBuilder->addFilter(
                    ExportEntityInterface::EXPIRED_AT,
                    (new \DateTime())->format(DateTime::DATE_PHP_FORMAT),
                    'lteq'
                );
                $exportList = $this->exportEntityRepository->getList($this->searchCriteriaBuilder->create());

                /** @var ExportEntityInterface $exportEntity */
                foreach ($exportList->getItems() as $exportEntity) {
                    $this->exportEntityRepository->delete($exportEntity);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }
}
