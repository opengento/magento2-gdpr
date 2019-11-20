<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * @api
 */
interface NotifierInterface
{
    public function notify(ExportEntityInterface $exportEntity): void;
}
