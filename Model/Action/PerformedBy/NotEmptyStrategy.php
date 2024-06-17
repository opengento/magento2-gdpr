<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Opengento\Gdpr\Model\Action\PerformedByInterface;

class NotEmptyStrategy implements PerformedByInterface
{
    private const PERFORMED_BY = 'Unknown';

    /**
     * @var PerformedByInterface[]
     */
    private array $performedByList;

    public function __construct(
        array $performedByList
    ) {
        $this->performedByList = $performedByList;
    }

    public function get(): string
    {
        $performer = self::PERFORMED_BY;

        foreach ($this->performedByList as $performedBy) {
            $performer = $performedBy->get() ?: $performer;
        }

        return $performer;
    }
}
