<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Delete;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Gdpr\Helper\Data;

/**
 * Class CustomerDataProcessor
 */
class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Opengento\Gdpr\Helper\Data $helperData
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Data $helperData,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $entityId): bool
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        return $this->customerRepository->deleteById($entityId);
    }
}
