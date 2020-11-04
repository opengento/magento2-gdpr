<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use function array_reduce;

final class ArrayValue implements AnonymizerInterface
{
    /**
     * @var AnonymizerInterface[]
     */
    private $anonymizers;

    public function __construct(
        array $anonymizers
    ) {
        $this->anonymizers = $anonymizers;
    }

    public function anonymize($value): array
    {
        return array_reduce(
            $this->anonymizers,
            static function ($array, AnonymizerInterface $anonymizer) use ($value): array {
                $array[] = $anonymizer->anonymize($value);

                return $array;
            },
            []
        );
    }
}
