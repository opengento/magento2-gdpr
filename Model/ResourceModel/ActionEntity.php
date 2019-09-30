<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use function is_array;
use function json_decode;
use function json_encode;

class ActionEntity extends AbstractDb
{
    public const TABLE = 'opengento_gdpr_action_entity';

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ActionEntityInterface::ID);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        $object->setData(
            ActionEntityInterface::PARAMETERS,
            json_decode($object->getData(ActionEntityInterface::PARAMETERS), true)
        );

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(AbstractModel $object)
    {
        $parameters = $object->getData(ActionEntityInterface::PARAMETERS);
        if (is_array($parameters)) {
            $object->setData(ActionEntityInterface::PARAMETERS, json_encode($parameters));
        }
        if (!$object->hasData(ActionEntityInterface::STATE)) {
            $object->setData(ActionEntityInterface::STATE, ActionEntityInterface::STATE_PENDING);
        }

        return parent::_beforeSave($object);
    }
}
