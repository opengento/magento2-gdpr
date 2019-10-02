<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;

class ActionEntity extends AbstractDb
{
    public const TABLE = 'opengento_gdpr_action_entity';

    /**
     * @var State
     */
    private $areaState;

    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        State $areaState,
        ?string $connectionName = null
    ) {
        $this->areaState = $areaState;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ActionEntityInterface::ID);
        $this->_serializableFields = [ActionEntityInterface::PARAMETERS => [[], []]];
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object): AbstractDb
    {
        $object->setData(ActionEntityInterface::PERFORMED_FROM, $this->areaState->getAreaCode());

        return parent::_beforeSave($object);
    }
}
