<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

final class OrderDataProcessor extends AbstractDataProcessor
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        parent::__construct($dataCollector);
    }

    public function execute(int $customerId, array $data): array
    {
        $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($this->criteriaBuilder->create());

        /** @var Order $order */
        foreach ($orderList->getItems() as $order) {
            $key = 'order_id_' . $order->getEntityId();
            $data['orders'][$key] = $this->collectData($order);

            /** @var OrderAddressInterface|null $orderAddress */
            foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
                if ($orderAddress) {
                    $data['orders'][$key][$orderAddress->getAddressType()] = $this->collectData($orderAddress);
                }
            }
        }

        return $data;
    }
}
