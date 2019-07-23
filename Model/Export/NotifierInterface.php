<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Interface NotifierInterface
 * @api
 */
interface NotifierInterface
{
    /**
     * Notify the user of the export action
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $exportEntity
     * @return void
     */
    public function notify(ExportEntityInterface $exportEntity): void;
}
