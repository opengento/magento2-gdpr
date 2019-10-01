<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;

class ActionEntity extends AbstractDb
{
    public const TABLE = 'opengento_gdpr_action_entity';

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ActionEntityInterface::ID);
        $this->_serializableFields = [ActionEntityInterface::PARAMETERS => [[], []]];
    }
}
