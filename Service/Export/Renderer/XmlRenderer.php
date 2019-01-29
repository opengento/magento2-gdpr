<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Filesystem;
use Opengento\Gdpr\Service\Export\AbstractRenderer;

/**
 * Class XmlRenderer
 */
final class XmlRenderer extends AbstractRenderer
{
    /**
     * @var \Magento\Framework\Convert\ConvertArray
     */
    private $convertArray;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Convert\ConvertArray $convertArray
     */
    public function __construct(
        Filesystem $filesystem,
        ConvertArray $convertArray
    ) {
        $this->convertArray = $convertArray;
        parent::__construct($filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data): string
    {
        return $this->convertArray->assocToXml($data)->__toString();
    }
}
