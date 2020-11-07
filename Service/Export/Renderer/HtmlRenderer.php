<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Exception;
use InvalidArgumentException;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Framework\View\Page\Config\RendererFactory;
use Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer\LayoutInitiatorInterface;
use function extract;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;

final class HtmlRenderer extends AbstractRenderer
{
    /**
     * @var LayoutInitiatorInterface
     */
    private $layoutInitiator;

    /**
     * @var Renderer
     */
    private $pageConfigRenderer;

    /**
     * @var InlineInterface
     */
    private $translateInline;

    /**
     * @var ViewFileSystem
     */
    private $viewFileSystem;

    /**
     * @var string
     */
    private $template;

    public function __construct(
        Filesystem $filesystem,
        LayoutInitiatorInterface $layoutInitiator,
        Config $pageConfig,
        RendererFactory $rendererFactory,
        InlineInterface $translateInline,
        ViewFileSystem $viewFileSystem,
        string $template
    ) {
        $this->layoutInitiator = $layoutInitiator;
        $this->pageConfigRenderer = $rendererFactory->create(['pageConfig' => $pageConfig]);
        $this->translateInline = $translateInline;
        $this->viewFileSystem = $viewFileSystem;
        $this->template = $template;
        parent::__construct($filesystem, 'html');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function render(array $data): string
    {
        $layout = $this->layoutInitiator->createLayout();

        $addBlock = $layout->getBlock('head.additional');
        $requireJs = $layout->getBlock('require.js');
        /** @var Template $block */
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
     * @param array $viewVars
     * @return string
     * @throws Exception
     */
    private function renderPage(array $viewVars): string
    {
        $fileName = $this->viewFileSystem->getTemplateFileName($this->template);
        if (!$fileName) {
            throw new InvalidArgumentException('Template "' . $this->template . '" is not found');
        }

        ob_start();
        try {
            extract($viewVars, EXTR_SKIP);
            include $fileName;
        } catch (Exception $exception) {
            ob_end_clean();
            throw $exception;
        }

        return ob_get_clean();
    }
}
