<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer;

use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Layout\Reader;

/**
 * Class LayoutInitiator
 */
final class LayoutInitiator implements LayoutInitiatorInterface
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Framework\View\Layout\BuilderFactory
     */
    private $layoutBuilderFactory;

    /**
     * @var \Magento\Framework\View\Layout\ReaderPool
     */
    private $layoutReaderPool;

    /**
     * @var \Magento\Framework\View\Layout\GeneratorPool
     */
    private $layoutGeneratorPool;

    /**
     * @var \Magento\Framework\View\Page\Layout\Reader
     */
    private $pageLayoutReader;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var string
     */
    private $defaultLayoutHandle;

    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Layout\ReaderPool $layoutReaderPool
     * @param \Magento\Framework\View\Layout\GeneratorPool $layoutGeneratorPool
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param string $defaultLayoutHandle
     */
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

    /**
     * Create and initialize the page layout instructions
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    public function createLayout(): LayoutInterface
    {
        $layout = $this->layoutFactory->create([
            'cacheable' => false,
            'reader' => $this->layoutReaderPool,
            'generatorPool' => $this->layoutGeneratorPool,
        ]);

        $layout->getUpdate()->addHandle('default');
        $layout->getUpdate()->addHandle($this->defaultLayoutHandle);
        /** @var \Magento\Framework\View\Model\Layout\Merge $update */
        $update = $layout->getUpdate();
        if ($update->isLayoutDefined()) {
            $update->removeHandle('default');
        }

        return $this->addConfigLayout($layout);
    }

    /**
     * Add the default configuration to the page layout
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return \Magento\Framework\View\LayoutInterface
     */
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

        /** @var \Magento\Framework\View\Model\Layout\Merge $update */
        $update = $layout->getUpdate();
        $pageLayout = $this->pageConfig->getPageLayout() ?: $update->getPageLayout();

        if (!$pageLayout) {
            throw new \LogicException('Page layout is missing.');
        }

        $this->pageConfig->addBodyClass(\str_replace('_', '-', $this->defaultLayoutHandle));
        $this->pageConfig->addBodyClass('page-layout-' . $pageLayout);

        return $layout;
    }
}
