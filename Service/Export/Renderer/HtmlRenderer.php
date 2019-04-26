<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\RendererFactory;
use Opengento\Gdpr\Service\Export\AbstractRenderer;
use Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer\LayoutInitiatorInterface;

/**
 * Class HtmlRenderer
 */
final class HtmlRenderer extends AbstractRenderer
{
    /**
     * @var \Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer\LayoutInitiatorInterface
     */
    private $layoutInitiator;

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
     * @param \Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer\LayoutInitiatorInterface $layoutInitiator
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\Page\Config\RendererFactory $pageConfigRendererFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param string $template
     */
    public function __construct(
        Filesystem $filesystem,
        LayoutInitiatorInterface $layoutInitiator,
        Config $pageConfig,
        RendererFactory $pageConfigRendererFactory,
        InlineInterface $translateInline,
        ViewFileSystem $viewFileSystem,
        string $template
    ) {
        $this->layoutInitiator = $layoutInitiator;
        $this->pageConfigRenderer = $pageConfigRendererFactory->create(['pageConfig' => $pageConfig]);
        $this->translateInline = $translateInline;
        $this->viewFileSystem = $viewFileSystem;
        $this->template = $template;
        parent::__construct($filesystem, 'html');
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function render(array $data): string
    {
        $layout = $this->layoutInitiator->createLayout();

        $addBlock = $layout->getBlock('head.additional');
        $requireJs = $layout->getBlock('require.js');
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $layout->getBlock('main.content.customer.privacy.export.personal.data');
        $block->setData('viewModel', new DataObject($data));

        $output = $this->renderPage([
            'requireJs' => $requireJs ? $requireJs->toHtml() : null,
            'headContent' => $this->pageConfigRenderer->renderHeadContent(),//todo replace style to inline css
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
}
