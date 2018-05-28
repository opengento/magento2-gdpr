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
use Flurrybox\EnhancedPrivacy\Helper\AccountData;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Process customer address data.
 */
class CustomerAddresses implements DataDeleteInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * CustomerAddresses constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        $customer = $this->customerRepository->getById($customer->getId());
        $addresses = $customer->getAddresses();

        if (!$addresses) {
            return;
        }

        foreach ($addresses as $address) {
            $this->addressRepository->delete($address);
        }
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function anonymize(CustomerInterface $customer)
    {
        $customer = $this->customerRepository->getById($customer->getId());
        $addresses = $customer->getAddresses();

        if (!$addresses) {
            return;
        }

        foreach ($addresses as $address) {
            $address
                ->setCity(AccountData::ANONYMOUS_STR)
                ->setCompany(AccountData::ANONYMOUS_STR)
                ->setCountryId('US')
                ->setFax(AccountData::ANONYMOUS_STR)
                ->setPrefix(AccountData::ANONYMOUS_STR)
                ->setFirstname(AccountData::ANONYMOUS_STR)
                ->setLastname(AccountData::ANONYMOUS_STR)
                ->setMiddlename(AccountData::ANONYMOUS_STR)
                ->setSuffix(AccountData::ANONYMOUS_STR)
                ->setPostcode(AccountData::ANONYMOUS_STR)
                ->setRegionId(1)
                ->setStreet([AccountData::ANONYMOUS_STR, AccountData::ANONYMOUS_STR])
                ->setTelephone(AccountData::ANONYMOUS_STR);
        }

        $this->customerRepository->save($customer);
    }
}
