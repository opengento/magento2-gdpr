<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\Phrase;

/**
 * Class ExportRenderer
 */
class ExportRenderer implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $rendererPool;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $rendererPool
     */
    public function __construct(
        TMap $rendererPool
    ) {
        $this->rendererPool = $rendererPool;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var \Opengento\Gdpr\Service\Export\RendererInterface $renderer */
            foreach ($this->rendererPool->getIterator() as $code => $renderer) {
                $this->options[] = ['value' => $code, 'label' => new Phrase($code)];
            }
        }

        return $this->options;
    }
}
