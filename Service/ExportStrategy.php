<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportStrategy
 * @api
 */
class ExportStrategy implements RendererInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $rendererPool;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $renderer;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $rendererPool
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        TMap $rendererPool,
        Config $config
    ) {
        $this->rendererPool = $rendererPool;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data): string
    {
        return $this->resolveRenderer()->render($data);
    }

    /**
     * {@inheritdoc}
     */
    public function saveData(string $fileName, array $data): string
    {
        $fileName .= '.' . $this->config->getExportRendererCode();
        return $this->resolveRenderer()->saveData($fileName, $data);
    }

    /**
     * Resolve and retrieve the current renderer
     *
     * @return \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private function resolveRenderer(): RendererInterface
    {
        if (!$this->renderer) {
            $rendererCode = $this->config->getExportRendererCode();

            if (!$this->rendererPool->offsetExists($rendererCode)) {
                throw new \InvalidArgumentException(\sprintf('Unknown renderer type "%s".', $rendererCode));
            }

            $this->renderer = $this->rendererPool->offsetGet($rendererCode);
        }

        return $this->renderer;
    }
}
