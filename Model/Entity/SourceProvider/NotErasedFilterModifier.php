<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use function sprintf;

final class NotErasedFilterModifier implements ModifierInterface
{
    private const DEFAULT_PRIMARY_FIELD = 'entity_id';

    private const DEFAULT_MAIN_TABLE_ALIAS = 'main_table';

    private const JOIN_ON = '%s.%s=ogee.entity_id AND ogee.entity_type="%s"';

    private string $entityType;

    private string $entityPrimaryField;

    private string $mainTableAlias;

    public function __construct(
        string $entityType,
        string $entityPrimaryField = self::DEFAULT_PRIMARY_FIELD,
        string $mainTableAlias = self::DEFAULT_MAIN_TABLE_ALIAS
    ) {
        $this->entityType = $entityType;
        $this->entityPrimaryField = $entityPrimaryField;
        $this->mainTableAlias = $mainTableAlias;
    }

    public function apply(Collection $collection, Filter $filter): void
    {
        if ($collection instanceof AbstractDb) {
            $connection = $collection->getConnection();
            $select = $collection->getSelect();
            $select->joinLeft(
                ['ogee' => $connection->getTableName('opengento_gdpr_erase_entity')],
                sprintf(self::JOIN_ON, $this->mainTableAlias, $this->entityPrimaryField, $this->entityType),
                ['']
            );
            $select->where('ogee.erase_id IS NULL');
        }
    }
}
