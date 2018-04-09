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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ReviewFactory;

/**
 * Export customer reviews.
 */
class CustomerReviews implements DataExportInterface
{
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * CustomerReviews constructor.
     *
     * @param ReviewFactory $reviewFactory
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
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
     */
    public function export(CustomerInterface $customer)
    {
        $collection = $this->collectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId());

        if (!$collection->getSize()) {
            return null;
        }

        $data[] = ['PRODUCT', 'NAME', 'TITLE', 'DETAILS'];

        foreach ($collection as $review) {
            /** @var \Magento\Review\Model\Review $review */
            $data[] = [
                $this->getReviewProduct($review->getId()),
                $review->getData('nickname'),
                $review->getData('title'),
                $review->getData('detail')
            ];
        }

        return $data;
    }

    /**
     * Get review product name.
     *
     * @param int $reviewId
     *
     * @return null|string
     */
    protected function getReviewProduct(int $reviewId)
    {
        $review = $this->reviewFactory->create();
        $review->getResource()->load($review, $reviewId);

        try {
            $product = $this->productRepository->getById($review->getEntityPkValue());
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $product->getName() . ' (' . $product->getSku() . ')';
    }
}
