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
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Export customer quote data.
 */
class CustomerQuote implements DataExportInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * CustomerQuote constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->cartRepository = $cartRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        $collection = $this->cartRepository
            ->getList($this->searchCriteriaBuilder->addFilter('customer_id', $customer->getId())->create())
            ->getItems();
        $data[] = ['PRODUCT NAME', 'SKU', 'QUANTITY', 'PRICE'];

        if (!$collection) {
            return null;
        }

        foreach ($collection as $cartItem) {
            $data[] = [
                $cartItem->getName(),
                $cartItem->getSku(),
                $cartItem->getQty(),
                $cartItem->getPrice()
            ];
        }

        return $data;
    }
}
