<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\ActionEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\ActionEntity;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity as ActionEntityResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ActionEntity::class, ActionEntityResourceModel::class);
        $this->_setIdFieldName(ActionEntityInterface::ID);
    }
}
