<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:53
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Delete;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Flurrybox\EnhancedPrivacy\Helper\Data;

/**
 * Class CustomerDataProcessor
 */
class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Flurrybox\EnhancedPrivacy\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Flurrybox\EnhancedPrivacy\Helper\Data $helperData
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
