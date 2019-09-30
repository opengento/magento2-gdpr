<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;

/**
 * @api
 */
interface ActionEntityManagementInterface
{
    /**
     * @param ActionEntityInterface $actionEntity
     * @return ActionEntityInterface
     * @throws LocalizedException
     */
    public function execute(ActionEntityInterface $actionEntity): ActionEntityInterface;

    /**
     * @param ActionEntityInterface $actionEntity
     * @param DateTime $scheduledAt
     * @return ActionEntityInterface
     * @throws LocalizedException
     */
    public function schedule(ActionEntityInterface $actionEntity, DateTime $scheduledAt): ActionEntityInterface;
}
