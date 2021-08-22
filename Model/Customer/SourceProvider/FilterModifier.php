<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\SourceProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierInterface;

final class FilterModifier implements ModifierInterface
{
    public function apply(Collection $collection, Filter $filter): void
    {
        if ($collection instanceof Collection\AbstractDb && $filter->getField() === 'created_at') {
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
            $collection->getSelect()->joinLeft(
                ['cl' => $connection->getTableName('customer_log')],
                'e.entity_id=cl.customer_id',
                null
            );
            $collection->getSelect()->where(
                $connection->prepareSqlCondition(
                    'IFNULL(cv.last_visit_at, GREATEST(IFNULL(cl.last_login_at, e.created_at), IFNULL(cl.last_logout_at, e.updated_at)))',
                    [$filter->getConditionType() => $filter->getValue()]
                )
            );
        }
    }
}
