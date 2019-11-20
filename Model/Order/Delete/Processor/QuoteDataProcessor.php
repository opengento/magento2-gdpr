<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Delete\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(int $orderId): bool
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $this->quoteRepository->delete($this->quoteRepository->get($order->getQuoteId()));
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
