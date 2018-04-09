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

namespace Flurrybox\EnhancedPrivacy\Model\Privacy\Export;

use Flurrybox\EnhancedPrivacy\Api\DataExportInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;

/**
 * Export customer wishlist.
 */
class CustomerWishlist implements DataExportInterface
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * CustomerWishlist constructor.
     *
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function export(CustomerInterface $customer)
    {
        $wishlistData = $this->wishlistProvider->getWishlist()->getItemCollection()->getItems();

        if (!$wishlistData) {
            return null;
        }

        $wishlist[] = ['PRODUCT NAME', 'ADDED AT', 'QUANTITY', 'DESCRIPTION'];

        foreach ($wishlistData as $item) {
            $wishlist[] = [
                $this->productRepository->getById($item['product_id'])->getName(),
                $item->getData('added_at'),
                $item->getData('qty'),
                $item->getData('description')
            ];
        }

        return $wishlist;
    }
}
