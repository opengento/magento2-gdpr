<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Cron schedule model.
 */
class CronSchedule extends AbstractModel
{
    const DELETE = 1;
    const ANONYMIZE = 2;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CronSchedule::class);
    }
}
