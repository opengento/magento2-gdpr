<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use function array_merge;
use function array_reduce;
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
     * ActionComposite constructor.
     *
     * @param ActionInterface[] $actions
     * @param ContextBuilder $contextBuilder
     */
    public function __construct(
        array $actions,
        ContextBuilder $contextBuilder
    ) {
        $this->actions = (static function (ActionInterface ...$actions): array {
            return $actions;
        })(...array_values($actions));
        $this->contextBuilder = $contextBuilder;
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        return array_reduce(
            $this->actions,
            static function (ActionContextInterface $context, ActionInterface $action): ActionContextInterface {
                $result = $action->execute($context);

                $this->contextBuilder->setPerformedBy($context->getPerformedBy());
                $this->contextBuilder->setParameters(array_merge($context->getParameters(), $result->getResult()));
                $this->contextBuilder->setScheduledAt($context->getScheduledAt());

                return $this->contextBuilder->create();
            },
            $actionContext
        );
    }
}
