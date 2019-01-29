<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Math\Random;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\AddressRepository;
use Magento\Sales\Model\OrderRepository;
use Opengento\Gdpr\Service\Anonymize\AnonymizeTool;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class OrderDataProcessor
 */
final class OrderDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizeTool
     */
    private $anonymizeTool;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\AddressRepository
     */
    private $orderAddressRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizeTool $anonymizeTool
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Order\AddressRepository $orderAddressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AnonymizeTool $anonymizeTool,
        OrderRepository $orderRepository,
        AddressRepository $orderAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->anonymizeTool = $anonymizeTool;
        $this->orderRepository = $orderRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(int $customerId): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($searchCriteria->create());
        $anonymousValue = $this->anonymizeTool->anonymousValue();

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderList->getItems() as $order) {
            $order->setCustomerFirstname($anonymousValue);
            $order->setCustomerLastname($anonymousValue);
            $order->setCustomerMiddlename($anonymousValue);
            $order->setCustomerEmail($this->anonymizeTool->anonymousEmail());

            $this->orderRepository->save($order);

            /** @var \Magento\Sales\Api\Data\OrderAddressInterface $orderAddress */
            foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
                if ($orderAddress) {
                    $orderAddress->setFirstname($anonymousValue);
                    $orderAddress->setMiddlename($anonymousValue);
                    $orderAddress->setLastname($anonymousValue);
                    $orderAddress->setPostcode($this->anonymizeTool->randomValue(5, Random::CHARS_DIGITS));
                    $orderAddress->setCity($anonymousValue);
                    $orderAddress->setStreet([$anonymousValue]);
                    $orderAddress->setEmail($this->anonymizeTool->anonymousEmail());
                    $orderAddress->setTelephone($this->anonymizeTool->randomValue(10, Random::CHARS_DIGITS));

                    $this->orderAddressRepository->save($orderAddress);
                }
            }
        }

        return true;
    }
}
