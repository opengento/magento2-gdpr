<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

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

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $customerEmail, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_email', $customerEmail)->create();
        $orderCollection = $this->orderRepository->getList($searchCriteria);

        foreach($orderCollection as $order)
        {
            $invoiceCollection = $order->getInvoiceCollection();
            $shipmentCollection = $order->getShipmentsCollection();
            $creditmemoCollection = $order->getCreditmemosCollection();
        }

        return array_merge_recursive(
            $data,
            [
                'orders' => $orderCollection->toArray(),
                'invoice' => $invoiceCollection->toArray(),
                'shipment' => $shipmentCollection->toArray(),
                'creditmemo' => $creditmemoCollection->toArray(),
            ]
        );
    }
}
