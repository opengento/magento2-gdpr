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

final class OrderDataProcessor implements ProcessorInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var EraseSalesInformationInterface
     */
    private $eraseSalesInformation;

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
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($orderList->getItems() as $order) {
            $lastActive = new DateTime($order->getUpdatedAt());
            $this->eraseSalesInformation->isAlive($lastActive)
                ? $this->eraseSalesInformation->scheduleEraseEntity((int) $order->getEntityId(), 'order', $lastActive)
                : $this->orderRepository->delete($order);
        }

        return true;
    }
}
