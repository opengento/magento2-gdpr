<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use DateTime;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use function array_merge;
use function array_values;

final class ActionComposite implements ActionInterface
{
    /**
     * @var ActionInterface[]
     */
    private $actions;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var ResultBuilder
     */
    private $resultBuilder;

    /**
     * @param ActionInterface[] $actions
     * @param ContextBuilder $contextBuilder
     * @param ResultBuilder $resultBuilder
     */
    public function __construct(
        array $actions,
        ContextBuilder $contextBuilder,
        ResultBuilder $resultBuilder
    ) {
        $this->actions = (static function (ActionInterface ...$actions): array {
            return $actions;
        })(...array_values($actions));
        $this->contextBuilder = $contextBuilder;
        $this->resultBuilder = $resultBuilder;
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        foreach ($this->actions as $action) {
            $result = $action->execute($actionContext);

            $this->contextBuilder->setPerformedBy($actionContext->getPerformedBy());
            $this->contextBuilder->setParameters(array_merge($actionContext->getParameters(), $result->getResult()));
            $this->contextBuilder->setScheduledAt($actionContext->getScheduledAt());

            $actionContext = $this->contextBuilder->create();
        }

        return $result ?? $this->defaultResult();
    }

    private function defaultResult(): ActionResultInterface
    {
        $this->resultBuilder->setPerformedAt(new DateTime());
        $this->resultBuilder->setState(ActionEntityInterface::STATE_FAILED);
        $this->resultBuilder->setResult([]);

        return $this->resultBuilder->create();
    }
}
