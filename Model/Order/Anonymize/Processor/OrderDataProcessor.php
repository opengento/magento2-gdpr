<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Anonymize\Processor;

use DateTime;
use Exception;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Opengento\Gdpr\Api\EraseSalesInformationInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

class OrderDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private OrderRepositoryInterface $orderRepository;

    private OrderAddressRepositoryInterface $addressRepository;

    private EraseSalesInformationInterface $salesInformation;

    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        OrderAddressRepositoryInterface $addressRepository,
        EraseSalesInformationInterface $salesInformation
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->addressRepository = $addressRepository;
        $this->salesInformation = $salesInformation;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $lastActive = new DateTime($order->getUpdatedAt());

        if ($this->salesInformation->isAlive($lastActive)) {
            $this->salesInformation->scheduleEraseEntity((int)$order->getEntityId(), 'order', $lastActive);

            return true;
        }

        $this->orderRepository->save($this->anonymizer->anonymize($order));

        /** @var OrderAddressInterface|null $orderAddress */
        foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
            if ($orderAddress) {
                $this->addressRepository->save($this->anonymizer->anonymize($orderAddress));
            }
        }

        return true;
    }
}
