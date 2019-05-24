<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Export\RendererFactory;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class RendererStrategy
 */
final class RendererStrategy implements RendererInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\RendererFactory
     */
    private $exportRendererFactory;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $renderer;

    /**
     * @param \Opengento\Gdpr\Service\Export\RendererFactory $exportRendererFactory
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        RendererFactory $exportRendererFactory,
        Config $config
    ) {
        $this->exportRendererFactory = $exportRendererFactory;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function render(array $data): string
    {
        return $this->resolveRenderer()->render($data);
    }

    /**
     * @inheritdoc
     */
    public function saveData(string $fileName, array $data): string
    {
        return $this->resolveRenderer()->saveData($fileName, $data);
    }

    /**
     * Resolve and retrieve the current renderer
     *
     * @return \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private function resolveRenderer(): RendererInterface
    {
        return $this->renderer ??
            $this->renderer = $this->exportRendererFactory->create($this->config->getExportRendererCode());
    }
}
