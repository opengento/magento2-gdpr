<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Delete\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Service\Delete\ProcessorInterface;

/**
 * Class CustomerDataProcessor
 */
final class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        try {
            $this->customerRepository->deleteById($customerId);
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
