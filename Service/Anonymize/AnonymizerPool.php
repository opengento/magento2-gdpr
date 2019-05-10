<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

/**
 * Class AnonymizerPool
 */
final class AnonymizerPool
{
    /**#@+
     * Constants for the anonymizer key codes
     */
    public const DEFAULT_ANONYMIZER = 'default';
    /**#@-*/

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
        $this->anonymizers = (static function (AnonymizerInterface ...$anonymizers): array {
            return $anonymizers;
        })(...\array_values($anonymizers));

        $this->anonymizers = \array_combine(\array_keys($anonymizers), $this->anonymizers);
    }

    /**
     * Retrieve the full list of registered anonymizers
     *
     * @return \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface[]
     */
    public function getAnonymizers(): array
    {
        return $this->anonymizers;
    }

    /**
     * Retrieve the anonymizer instance by its key code
     *
     * @param string $key
     * @return \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    public function getAnonymizer(string $key): AnonymizerInterface
    {
        return $this->anonymizers[$key];
    }
}
