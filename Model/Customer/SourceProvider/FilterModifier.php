<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\SourceProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierInterface;

final class FilterModifier implements ModifierInterface
{
    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function apply(Collection $collection, Filter $filter): void
    {
        if ($collection instanceof Collection\AbstractDb && $filter->getField() === 'created_at') {
            $connection = $collection->getConnection();

            $customerVisitorSelect = $connection->select()
                ->from($connection->getTableName('customer_visitor'))
                ->columns(['customer_id' => 'customer_id', 'last_visit_at' => 'MAX(last_visit_at)'])
                ->group(['customer_id']);

            $collection->getSelect()->joinLeft(
                ['cv' => $customerVisitorSelect],
                'main_table.entity_id=cl.customer_id',
                null
            );
            $collection->getSelect()->joinLeft(
                ['cl' => $connection->getTableName('customer_log')],
                'main_table.entity_id=cl.customer_id',
                null
            );
            $collection->getSelect()->columns(
                [
                    'last_visit_at' => 'IFNULL(' .
                        'cv.last_visit_at,'.
                        'GREATEST(IFNULL(cl.last_login_at, e.created_at), IFNULL(cl.last_logout_at, e.updated_at))' .
                    ')',
                ]
            );
            $collection->addFieldToFilter('last_visit_at', [$filter->getConditionType() => $filter->getValue()]);
        }
    }
}
