<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use function sprintf;

final class AnonymizerFactory
{
    /**
     * Constants for the anonymizer key codes
     */
    public const DEFAULT_ANONYMIZER = 'default';

    /**
     * @var string[]
     */
    private $anonymizers;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        array $anonymizers,
        ObjectManagerInterface $objectManager
    ) {
        $this->anonymizers = $anonymizers;
        $this->objectManager = $objectManager;
    }

    public function get(string $anonymizerCode): AnonymizerInterface
    {
        if (!isset($this->anonymizers[$anonymizerCode])) {
            throw new InvalidArgumentException(sprintf('Unknown anonymizer type "%s".', $anonymizerCode));
        }

        return $this->objectManager->get($this->anonymizers[$anonymizerCode]);
    }
}
