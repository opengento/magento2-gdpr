<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types = 1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\AddressInterface;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\AddressRepository;

use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ResourceModel\Quote\Address;

use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\AbstractAnonymize;

/**
 * Class QuoteDataProcessor
 */
class SalesDataProcessor extends AbstractAnonymize implements ProcessorInterface
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Magento\Sales\Model\Order\AddressRepository
     */
    private $orderAddressRepository;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Address
     */
    private $quoteAddressResourceModel;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SalesDataProcessor constructor.
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Order\AddressRepository $orderAddressRepository
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        AddressRepository $orderAddressRepository,
        QuoteRepository $quoteRepository,
        Address $quoteAddressResourceModel,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteAddressResourceModel = $quoteAddressResourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $customerId): bool
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
            $orderCollection = $this->orderRepository->getList($searchCriteria->create());

            /** @var OrderInterface $order */
            foreach ($orderCollection as $order) {
                $this->hydrator->hydrate(
                    $order,
                    [
                        'customer_firstname' => $this->anonymousValue(),
                        'customer_lastname' => $this->anonymousValue(),
                        'customer_email' => $this->anonymousEmail(),
                    ]
                );
                $this->orderRepository->save($order);

                /** @var OrderAddressInterface $orderAddress */
                foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
                    if($orderAddress)
                    {
                        $this->hydrator->hydrate(
                            $orderAddress,
                            [
                                'firstname' => $this->anonymousValue(),
                                'lastname' => $this->anonymousValue(),
                                'postcode' => $this->anonymousValue(),
                                'city' => $this->anonymousValue(),
                                'street' => $this->anonymousValue(),
                                'telephone' => $this->anonymousValue(),
                                'email' => $this->anonymousEmail(),
                            ]
                        );
                        $this->orderAddressRepository->save($orderAddress);
                    }
                }
            }

            $quoteCollection = $this->quoteRepository->getList($searchCriteria->create());
            /** @var CartInterface $quote */
            foreach ($quoteCollection as $quote) {
                $this->hydrator->hydrate(
                    $quote,
                    [
                        'customer_firstname' => $this->anonymousValue(),
                        'customer_lastname' => $this->anonymousValue(),
                        'customer_email' => $this->anonymousEmail(),
                    ]
                );
                $this->quoteRepository->save($quote);

                /** @var AddressInterface $orderAddress */
                foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
                    $this->hydrator->hydrate(
                        $quoteAddress,
                        [
                            'firstname' => $this->anonymousValue(),
                            'lastname' => $this->anonymousValue(),
                            'postcode' => $this->anonymousValue(),
                            'city' => $this->anonymousValue(),
                            'street' => $this->anonymousValue(),
                            'telephone' => $this->anonymousValue(),
                            'email' => $this->anonymousEmail(),
                        ]
                    );
                    $this->quoteAddressResourceModel->save($quoteAddress);
                }
            }

        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }


}
