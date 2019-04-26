<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;
use Opengento\Gdpr\Service\Export\AbstractRenderer;

/**
 * Class JsonRenderer
 */
final class JsonRenderer extends AbstractRenderer
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(
        Filesystem $filesystem,
        Json $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($filesystem, 'json');
    }

    /**
     * @inheritdoc
     */
    public function render(array $data): string
    {
        return $this->jsonSerializer->serialize($data);
    }
}
