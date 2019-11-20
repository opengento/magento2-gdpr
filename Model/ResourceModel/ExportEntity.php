<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use function is_array;

class ExportEntity extends AbstractDb
{
    public const TABLE = 'opengento_gdpr_export_entity';

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ExportEntityInterface::ID);
    }

    /**
     * @inheritdoc
     * @param string|array $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object): Select
    {
        if (!is_array($field) && !is_array($value)) {
            return parent::_getLoadSelect($field, $value, $object);
        }

        $select = $this->getConnection()->select()->from($this->getMainTable());

        foreach ($field as $i => $identifier) {
            $pk = $this->getConnection()->quoteIdentifier(\sprintf('%s.%s', $this->getMainTable(), $identifier));
            $select->where($pk . '=?', $value[$i]);
        }

        return $select;
    }
}
