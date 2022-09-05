<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer;

use LogicException;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Layout\Reader;
use function str_replace;

final class LayoutInitiator implements LayoutInitiatorInterface
{
    private LayoutFactory $layoutFactory;

    private BuilderFactory $layoutBuilderFactory;

    private ReaderPool $layoutReaderPool;

    private GeneratorPool $layoutGeneratorPool;

    private Reader $pageLayoutReader;

    private Config $pageConfig;

    private string $defaultLayoutHandle;

    public function __construct(
        LayoutFactory $layoutFactory,
        BuilderFactory $layoutBuilderFactory,
        ReaderPool $layoutReaderPool,
        GeneratorPool $layoutGeneratorPool,
        Reader $pageLayoutReader,
        Config $pageConfig,
        string $defaultLayoutHandle
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        $this->layoutReaderPool = $layoutReaderPool;
        $this->layoutGeneratorPool = $layoutGeneratorPool;
        $this->pageLayoutReader = $pageLayoutReader;
        $this->pageConfig = $pageConfig;
        $this->defaultLayoutHandle = $defaultLayoutHandle;
    }

    public function createLayout(): LayoutInterface
    {
        $layout = $this->layoutFactory->create([
            'cacheable' => false,
            'reader' => $this->layoutReaderPool,
            'generatorPool' => $this->layoutGeneratorPool,
        ]);

        $layout->getUpdate()->addHandle($this->defaultLayoutHandle);

        return $this->addConfigLayout($layout);
    }

    private function addConfigLayout(LayoutInterface $layout): LayoutInterface
    {
        $this->layoutBuilderFactory->create(
            BuilderFactory::TYPE_PAGE,
            [
                'layout' => $layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader,
            ]
        )->build();

        /** @var Merge $update */
        $update = $layout->getUpdate();
        $pageLayout = $this->pageConfig->getPageLayout() ?: $update->getPageLayout();

        if (!$pageLayout) {
            throw new LogicException('Page layout is missing.');
        }

        $this->pageConfig->addBodyClass(str_replace('_', '-', $this->defaultLayoutHandle));
        $this->pageConfig->addBodyClass('page-layout-' . $pageLayout);

        return $layout;
    }
}
