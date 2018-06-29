<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Filesystem;
use Magento\Framework\Json\Encoder;
use Opengento\Gdpr\Service\Export\AbstractRenderer;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class JsonRenderer
 */
class JsonRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * @var \Magento\Framework\Json\Encoder
     */
    private $jsonEncoder;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Json\Encoder $jsonEncoder
     */
    public function __construct(
        Filesystem $filesystem,
        Encoder $jsonEncoder
    ) {
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data): string
    {
        return $this->jsonEncoder->encode($data);
    }
}
