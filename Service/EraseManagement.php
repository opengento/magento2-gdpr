<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Opengento\Gdpr\Model\Config\Source\EraseComponents;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class EraseManagement
 * @api
 */
final class EraseManagement
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
     * Execute the processors by strategy type
     *
     * @param int $customerId
     * @return bool
     */
    public function erase(int $customerId): bool
    {
        foreach (\array_column($this->eraseComponents->toOptionArray(), 'value') as $component) {
            $this->eraseProcessor->execute($component, $customerId);
        }

        return true;
    }
}
