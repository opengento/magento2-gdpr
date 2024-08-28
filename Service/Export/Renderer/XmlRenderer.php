<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;

class XmlRenderer extends AbstractRenderer
{
    private const ROOT_NAME = 'data';

    private ConvertArray $convertArray;

    private string $rootName;

    public function __construct(
        Filesystem $filesystem,
        ConvertArray $convertArray,
        string $rootName = self::ROOT_NAME
    ) {
        $this->convertArray = $convertArray;
        $this->rootName = $rootName;
        parent::__construct($filesystem, 'xml');
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function render(array $data): string
    {
        return $this->convertArray->assocToXml($data, $this->rootName)->saveXML();
    }
}
