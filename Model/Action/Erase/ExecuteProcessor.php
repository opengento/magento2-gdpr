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

final class ExecuteProcessor implements ProcessorInterface
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
        $eraseEntity = ArgumentReader::getEntity($actionEntity);

        if ($eraseEntity === null) {
            throw new InvalidArgumentException('Argument "entity" is required.');
        }

        return [ArgumentReader::ERASE_ENTITY => $this->eraseEntityManagement->process($eraseEntity)];
    }
}
