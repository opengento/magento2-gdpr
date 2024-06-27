<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Delete\Processor;

use DateTime;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Api\EraseSalesInformationInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

class OrderDataProcessor implements ProcessorInterface
{
    private OrderRepositoryInterface $orderRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    private EraseSalesInformationInterface $salesInformation;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        EraseSalesInformationInterface $salesInformation
    ) {
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->salesInformation = $salesInformation;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($this->criteriaBuilder->create());

        foreach ($orderList->getItems() as $order) {
            $lastActive = new DateTime($order->getUpdatedAt());
            $this->salesInformation->isAlive($lastActive)
                ? $this->salesInformation->scheduleEraseEntity((int)$order->getEntityId(), 'order', $lastActive)
                : $this->orderRepository->delete($order);
        }

        return true;
    }
}
