<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export;

use Opengento\Gdpr\Model\Export\AbstractExportEntityDecorator;

/**
 * Class ExportCustomer
 */
final class ExportCustomer extends AbstractExportEntityDecorator
{
    private const ENTITY_TYPE = 'customer';

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }
}
