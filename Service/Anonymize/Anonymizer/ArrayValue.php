<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class ArrayValue
 */
class ArrayValue implements AnonymizerInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface[]
     */
    private $anonymizers;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface[] $anonymizers
     */
    public function __construct(
        array $anonymizers
    ) {
        $this->anonymizers = (static function (AnonymizerInterface ...$anonymizers) {
            return $anonymizers;
        })(... $anonymizers);
    }

    /**
     * @inheritdoc
     */
    public function anonymize($value): array
    {
        return \array_reduce(
            $this->anonymizers,
            static function ($array, AnonymizerInterface $anonymizer) use ($value) {
                $array[] = $anonymizer->anonymize($value);

                return $array;
            },
            []
        );
    }
}
