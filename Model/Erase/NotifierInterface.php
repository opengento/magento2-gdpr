<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * Interface NotifierInterface
 * @api
 */
interface NotifierInterface
{
    /**
     * Notify the user of the erase action
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $eraseEntity
     * @return void
     */
    public function notify(EraseEntityInterface $eraseEntity): void;
}
