<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

/**
 * Class ExportPersonalData
 */
final class ExportPersonalData extends AbstractExportEntityDecorator
{
    private const FILE_NAME = 'personal_data';

    /**
     * @inheritdoc
     */
    public function getFileName(): string
    {
        return self::FILE_NAME;
    }
}
