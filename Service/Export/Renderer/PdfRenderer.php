<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Exception;
use Magento\Framework\Filesystem;
use TCPDF;

final class PdfRenderer extends AbstractRenderer
{
    /**
     * @var HtmlRenderer
     */
    private $htmlRenderer;

    public function __construct(
        Filesystem $filesystem,
        HtmlRenderer $htmlRenderer
    ) {
        $this->htmlRenderer = $htmlRenderer;
        parent::__construct($filesystem, 'pdf');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function render(array $data): string
    {
        $pdf = new TCPDF();
        $pdf->AddPage('P', 'A4');
        $pdf->writeHTML($this->htmlRenderer->render($data));

        return $pdf->Output('', 'S');
    }
}
