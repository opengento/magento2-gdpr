<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\EraseEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\EraseEntity;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity as EraseEntityResourceModel;

/**
 * Class Collection
 */
final class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(EraseEntity::class, EraseEntityResourceModel::class);
        $this->_setIdFieldName(EraseEntityInterface::ID);
    }
}
