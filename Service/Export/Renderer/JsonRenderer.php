<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;

class JsonRenderer extends AbstractRenderer
{
    private Json $jsonSerializer;

    public function __construct(
        Filesystem $filesystem,
        Json $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($filesystem, 'json');
    }

    public function render(array $data): string
    {
        return $this->jsonSerializer->serialize($data);
    }
}
