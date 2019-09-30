<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use DateTime;
use Opengento\Gdpr\Api\ActionEntityManagementInterface;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use function array_merge;

final class Action implements ActionInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var ActionEntityBuilder
     */
    private $actionEntityBuilder;

    /**
     * @var ActionEntityManagementInterface
     */
    private $actionEntityManagement;

    /**
     * @var ResultBuilder
     */
    private $actionResultBuilder;

    public function __construct(
        string $type,
        ActionEntityBuilder $actionEntityBuilder,
        ActionEntityManagementInterface $actionEntityManagement,
        ResultBuilder $actionResultBuilder
    ) {
        $this->type = $type;
        $this->actionEntityBuilder = $actionEntityBuilder;
        $this->actionEntityManagement = $actionEntityManagement;
        $this->actionResultBuilder = $actionResultBuilder;
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $this->actionEntityBuilder->setType($this->type);
        $this->actionEntityBuilder->setParameters($actionContext->getParameters());
        $this->actionEntityBuilder->setPerformedBy($actionContext->getPerformedBy());
        $actionEntity = $this->actionEntityBuilder->create();

        $actionEntity = $actionContext->getScheduledAt()
            ? $this->actionEntityManagement->schedule($actionEntity, $actionContext->getScheduledAt())
            : $this->actionEntityManagement->execute($actionEntity);

        $this->actionResultBuilder->setState($actionEntity->getState());
        $this->actionResultBuilder->setPerformedAt(new DateTime($actionEntity->getPerformedAt()));
        $this->actionResultBuilder->setResult(
            array_merge(['message' => $actionEntity->getMessage()], $actionEntity->getResult())
        );

        return $this->actionResultBuilder->create();
    }
}
