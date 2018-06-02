<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class QuoteDataProcessor
 */
class SalesDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SalesDataProcessor constructor.
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $customerEmail, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_EMAIL, $customerEmail);
        $orderCollection = $this->orderRepository->getList($searchCriteria->create());
        $salesData = [];

        /** @var OrderInterface $order */
        foreach($orderCollection as $order) {
            $salesData[$order->getIncrementId()] = [
                'orders' => $orderCollection->toArray(),
                'invoice' => $order->getInvoiceCollection()->toArray(),
                'shipment' => $order->getShipmentsCollection()->toArray(),
                'creditmemo' => $order->getCreditmemosCollection()->toArray(),
            ];
        }

        return array_merge_recursive($data, ['sales' => $salesData]);
    }
}
