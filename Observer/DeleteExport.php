<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Observer;

use Exception;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Entity\EntityTypeResolver;
use Psr\Log\LoggerInterface;

final class DeleteExport implements ObserverInterface
{
    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var EntityTypeResolver
     */
    private $entityTypeResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ExportEntityRepositoryInterface $exportRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        EntityTypeResolver $entityTypeResolver,
        LoggerInterface $logger
    ) {
        $this->exportRepository = $exportRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->entityTypeResolver = $entityTypeResolver;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        /** @var AbstractModel $entity */
        $entity = $observer->getData('data_object');

        try {
            foreach ($this->fetchExportEntities($entity)->getItems() as $exportEntity) {
                $this->exportRepository->delete($exportEntity);
            }
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @param AbstractModel $entity
     * @return ExportEntitySearchResultsInterface
     * @throws LocalizedException
     * @throws Exception
     */
    private function fetchExportEntities(AbstractModel $entity): SearchResultsInterface
    {
        $entityTypes = $this->entityTypeResolver->resolve($entity);

        foreach ($entityTypes as $entityType => $idFieldName) {
            $this->criteriaBuilder->addFilters([
                $this->createEntityIdFilter((int) $entity->getData($idFieldName)),
                $this->createEntityTypeFilter($entityType)
            ]);
        }

        return $this->exportRepository->getList($this->criteriaBuilder->create());
    }

    private function createEntityIdFilter(int $entityId): Filter
    {
        $this->filterBuilder->setField(ExportEntityInterface::ENTITY_ID);
        $this->filterBuilder->setValue($entityId);
        $this->filterBuilder->setConditionType('eq');

        return $this->filterBuilder->create();
    }

    private function createEntityTypeFilter(string $entityType): Filter
    {
        $this->filterBuilder->setField(ExportEntityInterface::ENTITY_TYPE);
        $this->filterBuilder->setValue($entityType);
        $this->filterBuilder->setConditionType('eq');

        return $this->filterBuilder->create();
    }
}
