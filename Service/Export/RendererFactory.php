<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ExportFactory
 */
final class RendererFactory
{
    /**
     * @var string[]
     */
    private $renderers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $renderers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $renderers,
        ObjectManagerInterface $objectManager
    ) {
        $this->renderers = $renderers;
        $this->objectManager = $objectManager;
    }

    /**
     * Create a new export renderer
     *
     * @param string $rendererCode
     * @param array $arguments
     * @return \Opengento\Gdpr\Service\Export\RendererInterface
     */
    public function create(string $rendererCode, array $arguments = []): RendererInterface
    {
        if (!isset($this->renderers[$rendererCode])) {
            throw new \InvalidArgumentException(\sprintf('Unknown renderer type "%s".', $rendererCode));
        }

        return $this->objectManager->create($this->renderers[$rendererCode], $arguments);
    }
}
