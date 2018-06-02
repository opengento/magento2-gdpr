<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 18:46
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
