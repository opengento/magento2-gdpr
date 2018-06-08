<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\Reasons;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Opengento\Gdpr\Model\Reasons;

/**
 * Reasons collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'reason_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Reasons::class, \Opengento\Gdpr\Model\ResourceModel\Reasons::class);
    }
}
