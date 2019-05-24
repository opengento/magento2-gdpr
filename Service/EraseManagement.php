<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Opengento\Gdpr\Api\EraseInterface;
use Opengento\Gdpr\Model\Config\Source\EraseComponents;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class EraseManagement
 */
final class EraseManagement implements EraseInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    private $eraseProcessor;

    /**
     * @var \Opengento\Gdpr\Model\Config\Source\EraseComponents
     */
    private $eraseComponents;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorInterface $eraseProcessor
     * @param \Opengento\Gdpr\Model\Config\Source\EraseComponents $eraseComponents
     */
    public function __construct(
        ProcessorInterface $eraseProcessor,
        EraseComponents $eraseComponents
    ) {
        $this->eraseProcessor = $eraseProcessor;
        $this->eraseComponents = $eraseComponents;
    }

    /**
     * @inheritdoc
     */
    public function erase(int $customerId): bool
    {
        foreach (\array_column($this->eraseComponents->toOptionArray(), 'value') as $component) {
            if (!$this->eraseProcessor->execute($component, $customerId)) {
                return false;
            }
        }

        return true;
    }
}
