<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Api\Data\WebsiteInterface;

use function sprintf;

class NotErasedFilterModifier implements ModifierInterface
{
    private const DEFAULT_PRIMARY_FIELD = 'entity_id';
    private const DEFAULT_MAIN_TABLE_ALIAS = 'main_table';
    private const JOIN_ON = '%s.%s=ogee.entity_id AND ogee.entity_type="%s"';

    public function __construct(
        private string $entityType,
        private string $entityPrimaryField = self::DEFAULT_PRIMARY_FIELD,
        private string $mainTableAlias = self::DEFAULT_MAIN_TABLE_ALIAS
    ) {}

    public function apply(AbstractDb $collection, WebsiteInterface $website): void
    {
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
