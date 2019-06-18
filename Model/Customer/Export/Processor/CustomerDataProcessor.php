<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

/**
 * Class CustomerDataProcessor
 */
final class CustomerDataProcessor extends AbstractDataProcessor
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->customerRepository = $customerRepository;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $data['customer'] = $this->collectData($this->customerRepository->getById($customerId));

        return $data;
    }
}
