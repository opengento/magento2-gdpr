<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized as BackendArraySerialized;
use Magento\Framework\App\Config\Data\ProcessorInterface;

/**
 * Class ArraySerialized
 * @internal
 * @deprecated Create PR to Magento Github
 */
class ArraySerialized extends BackendArraySerialized implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function processValue($value)
    {
        return explode(',', $value ?? '');
    }
}
