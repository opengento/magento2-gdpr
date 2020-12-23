<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\View\Page\Config;

use Magento\Framework\View\Asset\AssetInterface;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Asset\PropertyGroup;
use Magento\Framework\View\Page\Config\Renderer as ViewRenderer;
use function file_get_contents;
use function sprintf;
use const PHP_EOL;

class Renderer extends ViewRenderer
{
    protected function renderAssetHtml(PropertyGroup $group): string
    {
        $assets = $this->processMerge($group->getAll(), $group);
        $attributes = $this->getGroupAttributes($group);
        $result = '';

        /** @var $asset AssetInterface */
        foreach ($assets as $asset) {
            $result .= $this->inlineHtml(
                $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
                $asset->getUrl(),
                $this->addDefaultAttributes($this->getAssetContentType($asset), $attributes)
            );
        }

        return $result;
    }

    private function inlineHtml(string $contentType, string $src, ?string $attributes): string
    {
        switch ($contentType) {
            case 'js':
                $result = '<script ' . $attributes . '>' . file_get_contents($src) . '</script>';
                break;

            case 'css':
                $result = '<style ' . $attributes . '>' . file_get_contents($src) . '</style>';
                break;
            default:
                $result = sprintf($this->getAssetTemplate($contentType, $attributes), $src);
                break;
        }

        return $result . PHP_EOL;
    }
}
