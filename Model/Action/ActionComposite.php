<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\ActionEntityBuilder;
use function array_merge;
use function array_values;

final class ActionComposite implements ActionInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var ActionInterface[]
     */
    private $actions;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var ActionEntityBuilder
     */
    private $actionEntityBuilder;

    /**
     * @var ResultBuilder
     */
    private $resultBuilder;

    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    /**
     * @param string $type
     * @param ActionInterface[] $actions
     * @param ContextBuilder $contextBuilder
     * @param ActionEntityBuilder $actionEntityBuilder
     * @param ResultBuilder $resultBuilder
     * @param ActionEntityRepositoryInterface $actionEntityRepository
     */
    public function __construct(
        string $type,
        array $actions,
        ContextBuilder $contextBuilder,
        ActionEntityBuilder $actionEntityBuilder,
        ResultBuilder $resultBuilder,
        ActionEntityRepositoryInterface $actionEntityRepository
    ) {
        $this->type = $type;
        $this->actions = (static function (ActionInterface ...$actions): array {
            return $actions;
        })(...array_values($actions));
        $this->contextBuilder = $contextBuilder;
        $this->actionEntityBuilder = $actionEntityBuilder;
        $this->resultBuilder = $resultBuilder;
        $this->actionEntityRepository = $actionEntityRepository;
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $this->actionEntityBuilder->setType($this->type);
        $this->actionEntityBuilder->setParameters($actionContext->getParameters());
        $this->actionEntityBuilder->setPerformedFrom($actionContext->getPerformedFrom());
        $this->actionEntityBuilder->setPerformedBy($actionContext->getPerformedBy());
        $this->actionEntityBuilder->setPerformedAt(new DateTime());

        try {
            foreach ($this->actions as $action) {
                $actionContext = $this->process($actionContext, $action);
            }
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_SUCCEEDED);
        } catch (LocalizedException $e) {
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_FAILED);
        }

        $actionEntity = $this->actionEntityRepository->save($this->actionEntityBuilder->create());

        return $this->createActionResult(
            $actionEntity->getState(),
            array_merge(['message' => $actionEntity->getMessage()], $actionEntity->getResult())
        );
    }

    /**
     * @param ActionContextInterface $actionContext
     * @param ActionInterface $action
     * @return ActionContextInterface
     * @throws LocalizedException
     */
    private function process(ActionContextInterface $actionContext, ActionInterface $action): ActionContextInterface
    {
        $result = $action->execute($actionContext);

        $this->contextBuilder->setPerformedBy($actionContext->getPerformedBy());
        $this->contextBuilder->setParameters(array_merge($actionContext->getParameters(), $result->getResult()));

        return $this->contextBuilder->create();
    }

    private function createActionResult(string $state, array $result): ActionResultInterface
    {
        $this->resultBuilder->setPerformedAt(new DateTime());
        $this->resultBuilder->setState($state);
        $this->resultBuilder->setResult($result);

        return $this->resultBuilder->create();
    }
}
