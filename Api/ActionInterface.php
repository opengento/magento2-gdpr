<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;

/**
 * @api
 * @todo remove
 */
interface ActionInterface
{
    /**
     * @param ActionContextInterface $actionContext
     * @return ActionResultInterface
     * @throws LocalizedException
     */
    public function execute(ActionContextInterface $actionContext): ActionResultInterface;
}
