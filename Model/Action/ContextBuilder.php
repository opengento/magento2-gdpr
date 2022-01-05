<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterfaceFactory;

/**
 * @api
 */
final class ContextBuilder
{
    /**
     * @var ActionContextInterfaceFactory
     */
    private ActionContextInterfaceFactory $actionContextFactory;

    private State $stateArea;

    /**
     * @var PerformedByInterface
     */
    private PerformedByInterface $performedBy;

    private array $data;

    public function __construct(
        ActionContextInterfaceFactory $actionContextFactory,
        State $stateArea,
        PerformedByInterface $performedBy
    ) {
        $this->actionContextFactory = $actionContextFactory;
        $this->stateArea = $stateArea;
        $this->performedBy = $performedBy;
        $this->data = [];
    }

    public function setPerformedFrom(string $performedFrom): ContextBuilder
    {
        $this->data['performedFrom'] = $performedFrom;

        return $this;
    }

    public function setPerformedBy(string $performedBy): ContextBuilder
    {
        $this->data['performedBy'] = $performedBy;

        return $this;
    }

    public function setParameters(array $parameters): ContextBuilder
    {
        $this->data['parameters'] = $parameters;

        return $this;
    }

    /**
     * @throws LocalizedException
     */
    public function create(): ActionContextInterface
    {
        if (!isset($this->data['performedFrom'])) {
            $this->data['performedFrom'] = $this->stateArea->getAreaCode();
        }
        if (!isset($this->data['performedBy'])) {
            $this->data['performedBy'] = $this->performedBy->get();
        }

        /** @var ActionContextInterface $context */
        $context = $this->actionContextFactory->create($this->data);
        $this->data = [];

        return $context;
    }
}
