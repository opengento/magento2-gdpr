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

class AnonymizerFactory
{
    public const DEFAULT_KEY = 'default';

    /**
     * @var string[]
     */
    private array $anonymizers;

    private ObjectManagerInterface $objectManager;

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
