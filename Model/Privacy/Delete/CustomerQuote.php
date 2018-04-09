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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Process customer quote data.
 */
class CustomerQuote implements DataDeleteInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CustomerQuote constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CartRepositoryInterface $cartRepository, StoreManagerInterface $storeManager)
    {
        $this->cartRepository = $cartRepository;
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
        $this->processQuote($customer->getId());
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
        $this->processQuote($customer->getId());
    }

    /**
     * Process quote.
     *
     * @param int $customerId
     *
     * @return void
     */
    protected function processQuote(int $customerId)
    {
        try {
            $quote = $this->cartRepository->getForCustomer($customerId, $this->getStoreIds());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $this->cartRepository->delete($quote);
    }

    /**
     * Get store ids.
     *
     * @return array
     */
    protected function getStoreIds()
    {
        $ids = [];

        foreach ($this->storeManager->getStores() as $store) {
            $ids[] = $store->getId();
        }

        return $ids;
    }
}
