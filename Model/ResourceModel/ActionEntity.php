<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Validator\ValidatorInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\Validator;

class ActionEntity extends AbstractDb
{
    public const TABLE = 'opengento_gdpr_action_entity';

    /**
     * @var Validator
     */
    private $validator;

    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Validator $validator,
        ?string $connectionName = null
    ) {
        $this->validator = $validator;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ActionEntityInterface::ID);
        $this->_serializableFields = [ActionEntityInterface::PARAMETERS => [[], []]];
    }

    public function getValidationRulesBeforeSave(): ValidatorInterface
    {
        return $this->validator;
    }
}
