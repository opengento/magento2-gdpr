<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Exception;
use Magento\Framework\Filesystem;
use mikehaertl\wkhtmlto\Pdf;
use mikehaertl\wkhtmlto\PdfFactory;
use RuntimeException;

final class PdfRenderer extends AbstractRenderer
{
    /**
     * @var PdfFactory
     */
    private $pdfFactory;

    /**
     * @var HtmlRenderer
     */
    private $htmlRenderer;

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
     * @throws Exception
     */
    public function render(array $data): string
    {
        /** @var Pdf $pdf */
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

        if (($result = $pdf->toString()) === false) {
            throw new RuntimeException('The PDF was not created successfully.');
        }

        return $result;
    }
}
