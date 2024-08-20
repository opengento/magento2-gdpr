<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\SourceProvider;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\Gdpr\Model\Config\Entity\Erasure as ErasureConfig;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierInterface;

class IdleFilterModifier implements ModifierInterface
{
    public function __construct(
        private ErasureConfig $erasureConfig
    ) {}

    /**
     * @throws Exception
     */
    public function apply(AbstractDb $collection, WebsiteInterface $website): void
    {
        $connection = $collection->getConnection();

        $visitorSelect = $connection->select()
            ->from(
                $connection->getTableName('customer_visitor'),
                ['customer_id' => 'customer_id', 'last_visit_at' => 'MAX(last_visit_at)']
            )
            ->group(['customer_id']);

        $collection->getSelect()->joinLeft(
            ['cv' => $visitorSelect],
            'e.entity_id=cv.customer_id',
            null
        );
        $collection->addFieldToFilter('website_id', $website->getId());
        $collection->getSelect()->joinLeft(
            ['cl' => $connection->getTableName('customer_log')],
            'e.entity_id=cl.customer_id',
            null
        );
        $collection->getSelect()->where(
            $connection->prepareSqlCondition(
                'IFNULL(cv.last_visit_at, GREATEST(IFNULL(cl.last_login_at, e.created_at), IFNULL(cl.last_logout_at, e.updated_at)))',
                ['lteq' => $this->erasureConfig->getEntityExpireDate($website->getId())]
            )
        );
    }
}
