<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizeTool;

/**
 * Class Email
 */
class Email implements AnonymizerInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizeTool
     */
    private $anonymizeTool;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizeTool $anonymizeTool
     */
    public function __construct(
        AnonymizeTool $anonymizeTool
    ) {
        $this->anonymizeTool = $anonymizeTool;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function anonymize($value): string
    {
        return $this->anonymizeTool->anonymousEmail($this->anonymizeTool->randomValue(3));
    }
}
