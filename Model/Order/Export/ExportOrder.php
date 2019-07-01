<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export;

use Opengento\Gdpr\Model\Export\AbstractExportEntityDecorator;

/**
 * Class ExportOrder
 */
final class ExportOrder extends AbstractExportEntityDecorator
{
    private const ENTITY_TYPE = 'order';

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }
}
