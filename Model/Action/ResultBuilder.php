<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterfaceFactory;

/**
 * @api
 */
final class ResultBuilder
{
    /**
     * @var ActionResultInterfaceFactory
     */
    private ActionResultInterfaceFactory $actionResultFactory;

    private array $data;

    public function __construct(
        ActionResultInterfaceFactory $actionResultFactory
    ) {
        $this->actionResultFactory = $actionResultFactory;
        $this->data = [];
    }

    public function setPerformedAt(DateTime $performedAt): ResultBuilder
    {
        $this->data['performedAt'] = $performedAt;

        return $this;
    }

    public function setState(string $state): ResultBuilder
    {
        $this->data['state'] = $state;

        return $this;
    }

    public function setMessage(string $message): ResultBuilder
    {
        $this->data['message'] = $message;

        return $this;
    }

    public function setResult(array $result): ResultBuilder
    {
        $this->data['result'] = $result;

        return $this;
    }

    public function create(): ActionResultInterface
    {
        /** @var ActionResultInterface $result */
        $result = $this->actionResultFactory->create($this->data);
        $this->data = [];

        return $result;
    }
}
