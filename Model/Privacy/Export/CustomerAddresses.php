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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\CountryFactory;

/**
 * Export customer addresses.
 */
class CustomerAddresses implements DataExportInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * CustomerAddresses constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CountryFactory $countryFactory
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, CountryFactory $countryFactory)
    {
        $this->customerRepository = $customerRepository;
        $this->countryFactory = $countryFactory;
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function export(CustomerInterface $customer)
    {
        $addresses = $this->customerRepository->getById($customer->getId())->getAddresses();

        if (!$addresses) {
            return null;
        }

        $data[] = [
            'CITY',
            'COMPANY',
            'COUNTRY',
            'FAX',
            'PREFIX',
            'FIRST NAME',
            'LAST NAME',
            'MIDDLE NAME',
            'SUFFIX',
            'POST CODE',
            'REGION',
            'STREET',
            'TEL'
        ];

        foreach ($addresses as $address) {
            $data[] = [
                $address->getCity(),
                $address->getCompany(),
                $this->countryFactory->create()->loadByCode($address->getCountryId())->getName(),
                $address->getFax(),
                $address->getPrefix(),
                $address->getFirstname(),
                $address->getLastname(),
                $address->getMiddlename(),
                $address->getSuffix(),
                $address->getPostcode(),
                $address->getRegion()->getRegion(),
                $address->getStreet()[0] . ' ' . $address->getStreet()[1] ?? null,
                $address->getTelephone()
            ];
        }

        return $data;
    }
}
