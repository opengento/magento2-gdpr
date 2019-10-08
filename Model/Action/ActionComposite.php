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
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_SUCCEEDED);
            $this->actionEntityBuilder->setResult($this->process($actionContext));
        } catch (LocalizedException $e) {
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_FAILED);
            $this->actionEntityBuilder->setMessage($e->getMessage());
        }

        $result = $this->result($this->actionEntityRepository->save($this->actionEntityBuilder->create()));

        if (isset($e)) {
            throw $e;
        }

        return $result;
    }

    /**
     * @param ActionContextInterface $actionContext
     * @return array
     * @throws LocalizedException
     */
    private function process(ActionContextInterface $actionContext): array
    {
        foreach ($this->actions as $action) {
            $this->contextBuilder->setPerformedFrom($actionContext->getPerformedFrom());
            $this->contextBuilder->setPerformedBy($actionContext->getPerformedBy());
            $this->contextBuilder->setParameters(
                array_merge($actionContext->getParameters(), $action->execute($actionContext)->getResult())
            );
            $actionContext = $this->contextBuilder->create();
        }

        return $actionContext->getParameters();
    }

    private function result(ActionEntityInterface $actionEntity): ActionResultInterface
    {
        $this->resultBuilder->setPerformedAt(new DateTime());
        $this->resultBuilder->setState($actionEntity->getState());
        $this->resultBuilder->setMessage($actionEntity->getMessage());
        $this->resultBuilder->setResult($actionEntity->getResult());

        return $this->resultBuilder->create();
    }
}
