<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;

abstract class AbstractAction implements ActionInterface
{
    private const DEFAULT_MESSAGE = '';

    /**
     * @var ResultBuilder
     */
    private ResultBuilder $resultBuilder;

    public function __construct(
        ResultBuilder $resultBuilder
    ) {
        $this->resultBuilder = $resultBuilder;
    }

    protected function createActionResult(
        array $result = [],
        string $message = self::DEFAULT_MESSAGE
    ): ActionResultInterface {
        $this->resultBuilder->setState(ActionEntityInterface::STATE_SUCCEEDED);
        $this->resultBuilder->setPerformedAt(new DateTime());
        $this->resultBuilder->setMessage($message);
        $this->resultBuilder->setResult($result);

        return $this->resultBuilder->create();
    }
}
