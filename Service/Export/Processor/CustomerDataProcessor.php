<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

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
     * @var \Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface
     */
    private $dataCollector;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataCollector = $dataCollector;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $data['customer'] = $this->dataCollector->collect($this->customerRepository->getById($customerId));

        return $data;
    }
}
