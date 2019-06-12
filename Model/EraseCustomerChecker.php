<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Api\EraseCustomerCheckerInterface;
use Opengento\Gdpr\Api\EraseCustomerRepositoryInterface;
use Opengento\Gdpr\Model\Customer\CustomerChecker;

/**
 * Class EraseCustomerChecker
 */
final class EraseCustomerChecker implements EraseCustomerCheckerInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface
     */
    private $eraseCustomerRepository;

    /**
     * @var \Opengento\Gdpr\Model\Customer\CustomerChecker
     */
    private $customerChecker;

    /**
     * @param \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface $eraseCustomerRepository
     * @param \Opengento\Gdpr\Model\Customer\CustomerChecker $customerChecker
     */
    public function __construct(
        EraseCustomerRepositoryInterface $eraseCustomerRepository,
        CustomerChecker $customerChecker
    ) {
        $this->eraseCustomerRepository = $eraseCustomerRepository;
        $this->customerChecker = $customerChecker;
    }

    /**
     * @inheritdoc
     */
    public function exists(int $customerId): bool
    {
        try {
            $this->eraseCustomerRepository->getByCustomerId($customerId);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function canCreate(int $customerId): bool
    {
        return !$this->exists($customerId) && !$this->customerChecker->hasPendingOrders($customerId);
    }

    /**
     * @inheritdoc
     */
    public function canCancel(int $customerId): bool
    {
        try {
            $entity = $this->eraseCustomerRepository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $entity->getState() === EraseCustomerInterface::STATE_PENDING
            && $entity->getStatus() === EraseCustomerInterface::STATUS_READY;
    }

    /**
     * @inheritdoc
     */
    public function canProcess(int $customerId): bool
    {
        try {
            $entity = $this->eraseCustomerRepository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return (($entity->getState() === EraseCustomerInterface::STATE_PENDING
                && $entity->getStatus() === EraseCustomerInterface::STATUS_READY)
            || ($entity->getState() === EraseCustomerInterface::STATE_PROCESSING
                && $entity->getStatus() === EraseCustomerInterface::STATUS_FAILED))
            && !$this->customerChecker->hasPendingOrders($entity->getCustomerId());
    }
}
