<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\EntityManager\Hydrator;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Anonymize\AbstractAnonymize;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class CustomerDataProcessor
 */
class CustomerDataProcessor extends AbstractAnonymize implements ProcessorInterface
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\EntityManager\Hydrator
     */
    private $hydrator;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\EntityManager\Hydrator $hydrator
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Hydrator $hydrator,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->hydrator = $hydrator;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $this->hydrator->hydrate(
                $customer,
                [
                    'firstname' => $this->anonymousValue(),
                    'lastname' => $this->anonymousValue(),
                    'email' => $this->anonymousEmail(),
                ]
            );

            $this->customerRepository->save($customer);

        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
