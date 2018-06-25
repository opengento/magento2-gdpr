<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Opengento\Gdpr\Service\Export\AbstractRenderer;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class PdfRenderer
 */
class PdfRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(array $data): string
    {
        // todo
        throw new \LogicException('Not implemented yet!');
    }
}
