<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer;

use Magento\Framework\View\LayoutInterface;

/**
 * @api
 */
interface LayoutInitiatorInterface
{
    public function createLayout(): LayoutInterface;
}
