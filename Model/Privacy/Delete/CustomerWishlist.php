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
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Wishlist\Model\WishlistFactory;

/**
 * Process customer wishlist.
 */
class CustomerWishlist implements DataDeleteInterface
{
    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * CustomerWishlist constructor.
     *
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(WishlistFactory $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processWishlist($customer->getId());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->processWishlist($customer->getId());
    }

    /**
     * Clear customer wishlist.
     *
     * @param int $customerId
     *
     * @return void
     * @throws \Exception
     */
    protected function processWishlist(int $customerId)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId);
        $wishlist->getResource()->delete($wishlist);
    }
}
