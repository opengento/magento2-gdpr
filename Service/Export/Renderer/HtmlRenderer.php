<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\LayoutFactory;
use Opengento\Gdpr\Service\Export\AbstractRenderer;

/**
 * Class HtmlRenderer
 */
final class HtmlRenderer extends AbstractRenderer
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Framework\View\Layout\BuilderFactory|\Magento\Framework\View\LayoutFactory
     */
    private $layoutBuilderFactory;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory
     */
    public function __construct(
        Filesystem $filesystem,
        LayoutFactory $layoutFactory,
        BuilderFactory $layoutBuilderFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        parent::__construct($filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data): string
    {
        $layout = $this->layoutFactory->create(['cacheable' => false]);
        $this->layoutBuilderFactory->create(BuilderFactory::TYPE_PAGE, ['layout' => $layout]);
        $layout->getUpdate()->addHandle('customer_privacy_export_personal_data');

        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $layout->getBlock('main.content.customer.privacy.export.personal.data');
        $block->setData('viewModel', new DataObject($data));

        return $layout->getOutput();
    }
}
