<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Erase;

use InvalidArgumentException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Model\Action\ProcessorInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;

final class CancelProcessor implements ProcessorInterface
{
    /**
     * @var EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    public function __construct(
        EraseEntityManagementInterface $eraseEntityManagement
    ) {
        $this->eraseEntityManagement = $eraseEntityManagement;
    }

    public function execute(ActionEntityInterface $actionEntity): array
    {
        $entityId = ArgumentReader::getEntityId($actionEntity);
        $entityType = ArgumentReader::getEntityType($actionEntity);

        if ($entityId === null || $entityType === null) {
            throw new InvalidArgumentException('Arguments "entity_id" and "entity_type" are required.');
        }

        return ['canceled' => $this->eraseEntityManagement->cancel($entityId, $entityType)];
    }
}
