<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;

/**
 * @api
 */
interface ProcessorInterface
{
    /**
     * @param ActionEntityInterface $actionEntity
     * @return array
     * @throws LocalizedException
     */
    public function execute(ActionEntityInterface $actionEntity): array;
}
