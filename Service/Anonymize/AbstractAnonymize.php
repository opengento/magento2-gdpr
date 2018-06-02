<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Anonymize;

use Magento\Framework\Phrase;

/**
 * Class AbstractAnonymize
 */
abstract class AbstractAnonymize implements ProcessorInterface
{
    /**
     * Retrieve an anonymous value
     *
     * @return string
     */
    protected function anonymousValue(): string
    {
        return (new Phrase('Anonymous'))->render();
    }
}
