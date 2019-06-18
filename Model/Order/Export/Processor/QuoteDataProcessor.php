<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;

/**
 * Class QuoteDataProcessor
 */
final class QuoteDataProcessor extends AbstractDataProcessor
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->quoteRepository = $quoteRepository;
        parent::__construct($orderRepository, $dataCollector);
    }

    /**
     * @inheritdoc
     */
    protected function export(OrderInterface $order, array $data): array
    {
        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($order->getQuoteId());
        } catch (NoSuchEntityException $e) {
            return $data;
        }

        $key = 'quote_id_' . $quote->getId();
        $data['quotes'][$key] = $this->collectData($quote);

        /** @var \Magento\Quote\Model\Quote\Address|null $quoteAddress */
        foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
            if ($quoteAddress) {
                $data['quotes'][$key][$quoteAddress->getAddressType()] = $this->collectData($quoteAddress);
            }
        }

        return $data;
    }
}
