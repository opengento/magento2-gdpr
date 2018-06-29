<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Service\Anonymize\AnonymizeTool;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class CustomerDataProcessor
 */
class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizeTool
     */
    private $anonymizeTool;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizeTool $anonymizeTool
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        AnonymizeTool $anonymizeTool,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->anonymizeTool = $anonymizeTool;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setFirstname($this->anonymizeTool->anonymousValue());
            $customer->setMiddlename($this->anonymizeTool->anonymousValue());
            $customer->setLastname($this->anonymizeTool->anonymousValue());
            $customer->setEmail($this->anonymizeTool->anonymousEmail());

            $this->customerRepository->save($customer);
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
