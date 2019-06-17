<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\EraseInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class EraseManagement
 */
final class EraseManagement implements EraseInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    private $processor;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorInterface $processor
     */
    public function __construct(
        ProcessorInterface $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function erase(int $customerId): bool
    {
        return $this->processor->execute($customerId);
    }
}
