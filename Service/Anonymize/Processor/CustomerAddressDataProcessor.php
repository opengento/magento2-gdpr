<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\AbstractAnonymize;

/**
 * Class CustomerAddressDataProcessor
 */
class CustomerAddressDataProcessor extends AbstractAnonymize implements ProcessorInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Opengento\Gdpr\Model\Config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $customerAddressRepository,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->config = $config;
    }

    public function execute(int $customerId): bool
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $addressCollection = $customer->getAddressesCollection();

            foreach($addressCollection as $address)
            {
                $this->hydrator->hydrate(
                    $address,
                    [
                        'firstname' => $this->anonymousValue(),
                        'lastname' => $this->anonymousValue(),
                        'street' => $this->anonymousValue(),
                        'city' => $this->anonymousValue(),
                        'telephone' => $this->anonymousValue(),
                        'postcode' => $this->anonymousValue()
                    ]
                );

                $this->customerAddressRepository->save($address);
            }
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
