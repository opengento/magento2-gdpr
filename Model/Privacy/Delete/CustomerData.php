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
use Flurrybox\EnhancedPrivacy\Helper\RandomGenerator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Process customer data.
 */
class CustomerData implements DataDeleteInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var RandomGenerator
     */
    protected $randomGenerator;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * CustomerData constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param RandomGenerator $randomGenerator
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RandomGenerator $randomGenerator,
        EncryptorInterface $encryptor
    ) {
        $this->customerRepository = $customerRepository;
        $this->randomGenerator = $randomGenerator;
        $this->encryptor = $encryptor;
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
        $this->customerRepository->deleteById($customer->getId());
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

        $customer
            ->setPrefix(AccountData::ANONYMOUS_STR)
            ->setFirstname(AccountData::ANONYMOUS_STR)
            ->setMiddlename(AccountData::ANONYMOUS_STR)
            ->setLastname(AccountData::ANONYMOUS_STR)
            ->setSuffix(AccountData::ANONYMOUS_STR)
            ->setCreatedAt(AccountData::ANONYMOUS_DATE)
            ->setUpdatedAt(AccountData::ANONYMOUS_DATE)
            ->setEmail($this->getAnonymousEmail($customer->getId()))
            ->setDob(AccountData::ANONYMOUS_DATE)
            ->setTaxvat(AccountData::ANONYMOUS_STR)
            ->setGender(0)
            ->setCustomAttribute('is_anonymized', true)
            ->setGroupId(GroupInterface::NOT_LOGGED_IN_ID);

        $this->customerRepository
            ->save($customer, $this->encryptor->getHash($this->randomGenerator->generateStr(64), true));
    }

    /**
     * Generate anonymized email.
     *
     * @param int $customerId
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAnonymousEmail(int $customerId)
    {
        return $customerId . $this->randomGenerator->generateStr() . '@' . AccountData::ANONYMOUS_STR . '.com';
    }
}
