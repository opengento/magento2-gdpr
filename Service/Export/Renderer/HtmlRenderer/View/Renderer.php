<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer\View;

use Magento\Framework\View\Asset\AssetInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Asset\PropertyGroup;
use Magento\Framework\View\Page\Config\Renderer as ViewRenderer;

use function sprintf;

use const PHP_EOL;

class Renderer extends ViewRenderer
{
    protected function renderAssetHtml(PropertyGroup $group): string
    {
        $contentType = $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE);
        $assets = $this->processMerge($group->getAll(), $group);
        $attributes = $this->getGroupAttributes($group);
        $result = '';

        /** @var $asset AssetInterface */
        foreach ($assets as $asset) {
            $result .= $asset instanceof File
                ? $this->inlineAsset($contentType, $asset, $attributes)
                : sprintf($this->getAssetTemplate($contentType, $attributes), $asset->getUrl()) . PHP_EOL;
        }

        return $result;
    }

    private function inlineAsset(string $contentType, File $asset, ?string $attributes): string
    {
        return $this->inlineHtml(
            $contentType,
            $asset,
            $this->addDefaultAttributes($this->getAssetContentType($asset), $attributes)
        );
    }

    private function inlineHtml(string $contentType, File $asset, ?string $attributes): string
    {
        switch ($contentType) {
            case 'js':
                $result = '<script' . $attributes . '>' . PHP_EOL . $asset->getContent() . '</script>';
                break;

            case 'css':
                $result = '<style' . $attributes . '>' .  PHP_EOL . $asset->getContent() . '</style>';
                break;
            default:
                $result = sprintf($this->getAssetTemplate($contentType, $attributes), $asset->getUrl());
                break;
        }

        return $result . PHP_EOL;
    }
}
