<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Opengento\Gdpr\Model\Action\PerformedByInterface;

final class NotEmptyStrategy implements PerformedByInterface
{
    private const PERFORMED_BY = 'Unknown';

    /**
     * @var PerformedByInterface[]
     */
    private $performedByList;

    /**
     * @param PerformedByInterface[] $performedByList
     */
    public function __construct(
        array $performedByList
    ) {
        $this->performedByList = (static function (PerformedByInterface ...$performedByList) {
            return $performedByList;
        })(...$performedByList);
    }

    public function get(): string
    {
        foreach ($this->performedByList as $performedBy) {
            $performer = $performedBy->get();

            if (!empty($performer)) {
                return $performer;
            }
        }

        return self::PERFORMED_BY;
    }
}
