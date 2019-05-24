<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Filesystem;
use mikehaertl\wkhtmlto\PdfFactory;

/**
 * Class PdfRenderer
 */
final class PdfRenderer extends AbstractRenderer
{
    /**
     * @var \mikehaertl\wkhtmlto\PdfFactory
     */
    private $pdfFactory;

    /**
     * @var \Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer
     */
    private $htmlRenderer;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \mikehaertl\wkhtmlto\PdfFactory $pdfFactory
     * @param \Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer $htmlRenderer
     */
    public function __construct(
        Filesystem $filesystem,
        PdfFactory $pdfFactory,
        HtmlRenderer $htmlRenderer
    ) {
        $this->pdfFactory = $pdfFactory;
        $this->htmlRenderer = $htmlRenderer;
        parent::__construct($filesystem, 'pdf');
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function render(array $data): string
    {
        /** @var \mikehaertl\wkhtmlto\Pdf $pdf */
        $pdf = $this->pdfFactory->create([
            'options' => [
                'ignoreWarnings' => true,
                'no-outline',
                'enable-external-links',
                'enable-internal-links',
                'encoding' => 'UTF-8',
                'margin-top' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'dpi' => 300,
                'zoom' => 1,
                'disable-smart-shrinking',
                'lowquality',
            ]
        ]);

        $pdf->addPage($this->htmlRenderer->render($data));

        return $pdf->toString();
    }
}
