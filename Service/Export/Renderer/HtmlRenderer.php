<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\RendererFactory;
use Magento\Framework\View\Page\Layout\Reader;
use Opengento\Gdpr\Service\Export\AbstractRenderer;

/**
 * Class HtmlRenderer
 */
final class HtmlRenderer extends AbstractRenderer
{
    private const DEFAULT_LAYOUT_HANDLE = 'customer_privacy_export_personal_data';

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
     * @var \Magento\Framework\View\Page\Config\Renderer
     */
    private $pageConfigRenderer;

    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    private $translateInline;

    /**
     * @var \Magento\Framework\View\FileSystem
     */
    private $viewFileSystem;

    /**
     * @var string
     */
    private $template;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Layout\ReaderPool $layoutReaderPool
     * @param \Magento\Framework\View\Layout\GeneratorPool $layoutGeneratorPool
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\Page\Config\RendererFactory $pageConfigRendererFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param string $template
     */
    public function __construct(
        Filesystem $filesystem,
        LayoutFactory $layoutFactory,
        BuilderFactory $layoutBuilderFactory,
        ReaderPool $layoutReaderPool,
        GeneratorPool $layoutGeneratorPool,
        Reader $pageLayoutReader,
        Config $pageConfig,
        RendererFactory $pageConfigRendererFactory,
        InlineInterface $translateInline,
        ViewFileSystem $viewFileSystem,
        string $template
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        $this->layoutReaderPool = $layoutReaderPool;
        $this->layoutGeneratorPool = $layoutGeneratorPool;
        $this->pageLayoutReader = $pageLayoutReader;
        $this->pageConfig = $pageConfig;
        $this->pageConfigRenderer = $pageConfigRendererFactory->create(['pageConfig' => $pageConfig]);
        $this->translateInline = $translateInline;
        $this->viewFileSystem = $viewFileSystem;
        $this->template = $template;
        parent::__construct($filesystem);
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function render(array $data): string
    {
        $layout = $this->addConfigLayout($this->initLayout($this->createLayout()));

        $addBlock = $layout->getBlock('head.additional');
        $requireJs = $layout->getBlock('require.js');
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $layout->getBlock('main.content.customer.privacy.export.personal.data');
        $block->setData('viewModel', new DataObject($data));

        $output = $this->renderPage([
            'requireJs' => $requireJs ? $requireJs->toHtml() : null,
            'headContent' => $this->pageConfigRenderer->renderHeadContent(),
            'headAdditional' => $addBlock ? $addBlock->toHtml() : null,
            'htmlAttributes' => $this->pageConfigRenderer->renderElementAttributes(Config::ELEMENT_TYPE_HTML),
            'headAttributes' => $this->pageConfigRenderer->renderElementAttributes(Config::ELEMENT_TYPE_HEAD),
            'bodyAttributes' => $this->pageConfigRenderer->renderElementAttributes(Config::ELEMENT_TYPE_BODY),
            'loaderIcon' => 'images/loader-2.gif',//todo
            'layoutContent' => $layout->getOutput(),
        ]);
        $this->translateInline->processResponseBody($output);
        
        return $output;
    }

    /**
     * Render the layout page to html output
     *
     * @param array $viewVars
     * @return string
     * @throws \Exception
     */
    private function renderPage(array $viewVars): string
    {
        $fileName = $this->viewFileSystem->getTemplateFileName($this->template);
        if (!$fileName) {
            throw new \InvalidArgumentException('Template "' . $this->template . '" is not found');
        }

        \ob_start();
        try {
            \extract($viewVars, EXTR_SKIP);
            include $fileName;
        } catch (\Exception $exception) {
            \ob_end_clean();
            throw $exception;
        }

        return \ob_get_clean();
    }

    /**
     * Add the default configuration to the page layout
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return \Magento\Framework\View\LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addConfigLayout(LayoutInterface $layout): LayoutInterface
    {
        $this->pageConfig->publicBuild();

        /** @var \Magento\Framework\View\Model\Layout\Merge $update */
        $update = $layout->getUpdate();
        $pageLayout = $this->pageConfig->getPageLayout() ?: $update->getPageLayout();

        if (!$pageLayout) {
            throw new LocalizedException(new Phrase('Page layout is missing.'));
        }

        $this->pageConfig->addBodyClass(\str_replace('_', '-', self::DEFAULT_LAYOUT_HANDLE));
        $this->pageConfig->addBodyClass('page-layout-' . $pageLayout);

        return $layout;
    }

    /**
     * Init the page layout instructions
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return \Magento\Framework\View\LayoutInterface
     */
    private function initLayout(LayoutInterface $layout): LayoutInterface
    {
        $layout->getUpdate()->addHandle('default');
        $layout->getUpdate()->addHandle(self::DEFAULT_LAYOUT_HANDLE);
        /** @var \Magento\Framework\View\Model\Layout\Merge $update */
        $update = $layout->getUpdate();
        if ($update->isLayoutDefined()) {
            $update->removeHandle('default');
        }

        return $layout;
    }

    /**
     * Create the page layout
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    private function createLayout(): LayoutInterface
    {
        $layout = $this->layoutFactory->create([
            'cacheable' => false,
            'reader' => $this->layoutReaderPool,
            'generatorPool' => $this->layoutGeneratorPool,
        ]);

        $this->layoutBuilderFactory->create(
            BuilderFactory::TYPE_PAGE,
            [
                'layout' => $layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader,
            ]
        );

        return $layout;
    }
}
