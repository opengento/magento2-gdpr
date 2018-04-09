<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Model\Privacy\Delete;

use Flurrybox\EnhancedPrivacy\Api\DataDeleteInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Process customer compare.
 */
class CustomerCompare implements DataDeleteInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CustomerCompare constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CollectionFactory $collectionFactory, StoreManagerInterface $storeManager)
    {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processCompare($customer->getId());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->processCompare($customer->getId());
    }

    /**
     * Delete customer compare items.
     *
     * @param int $customerId
     *
     * @return void
     */
    protected function processCompare(int $customerId)
    {
        foreach ($this->storeManager->getStores() as $store) {
            $this->collectionFactory
                ->create()
                ->useProductItem()
                ->setStoreId($store->getId())
                ->setCustomerId($customerId)
                ->setVisibility([
                    Visibility::VISIBILITY_IN_SEARCH,
                    Visibility::VISIBILITY_IN_CATALOG,
                    Visibility::VISIBILITY_BOTH
                ])
                ->walk('delete');
        }
    }
}
