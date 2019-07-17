<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Delete\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Erase\EraseSalesInformationInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class OrderDataProcessor
 */
final class OrderDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Opengento\Gdpr\Model\Erase\EraseSalesInformationInterface
     */
    private $eraseSalesInformation;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Model\Erase\EraseSalesInformationInterface $eraseSalesInformation
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EraseSalesInformationInterface $eraseSalesInformation
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->eraseSalesInformation = $eraseSalesInformation;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(int $customerId): bool
    {
        $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($orderList->getItems() as $order) {
            $lastActive = new \DateTime($order->getUpdatedAt());
            if ($this->eraseSalesInformation->isAlive($lastActive)) {
                $this->eraseSalesInformation->scheduleEraseEntity((int) $order->getEntityId(), 'order', $lastActive);
            } else {
                $this->orderRepository->delete($order);
            }
        }

        return true;
    }
}
