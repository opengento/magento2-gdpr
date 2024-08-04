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
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Entity\EntityTypeResolver;
use Psr\Log\LoggerInterface;

class InvalidateExport implements ObserverInterface
{
    public function __construct(
        private ExportEntityRepositoryInterface $exportRepository,
        private ExportEntityManagementInterface $exportManagement,
        private SearchCriteriaBuilder $criteriaBuilder,
        private FilterBuilder $filterBuilder,
        private EntityTypeResolver $entityTypeResolver,
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer): void
    {
        $entity = $observer->getData('data_object');
        if ($entity instanceof DataObject) {
            try {
                foreach ($this->fetchExportEntities($entity)->getItems() as $exportEntity) {
                    $this->exportManagement->invalidate($exportEntity);
                }
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), ['exception' => $e]);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }
    }

    /**
     * @return ExportEntitySearchResultsInterface
     * @throws LocalizedException
     * @throws Exception
     */
    private function fetchExportEntities(DataObject $entity): SearchResultsInterface
    {
        $entityTypes = $this->entityTypeResolver->resolve($entity);

        foreach ($entityTypes as $entityType => $idFieldName) {
            $this->criteriaBuilder->addFilters([
                $this->createEntityIdFilter((int)$entity->getData($idFieldName)),
                $this->createEntityTypeFilter($entityType)
            ]);
        }
        $this->criteriaBuilder->addFilter(ExportEntityInterface::EXPORTED_AT, true, 'notnull');
        $this->criteriaBuilder->addFilter(ExportEntityInterface::FILE_PATH, true, 'notnull');

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
