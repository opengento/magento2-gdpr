<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Utils;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Class CustomAttributesFilterProcessor
 */
final class CustomAttributesFilterProcessor
{
    /**
     * @var \Opengento\Gdpr\Service\Export\Processor\Utils\DataFilterProcessor
     */
    private $dataFilterProcessor;

    /**
     * @param \Opengento\Gdpr\Service\Export\Processor\Utils\DataFilterProcessor $dataFilterProcessor
     */
    public function __construct(
        DataFilterProcessor $dataFilterProcessor
    ) {
        $this->dataFilterProcessor = $dataFilterProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $scheme, array $data = []): array
    {
        return isset($data[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES])
            ? $this->dataFilterProcessor->execute($scheme, $data[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES])
            : [];
    }
}
