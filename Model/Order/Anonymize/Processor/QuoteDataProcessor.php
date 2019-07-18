<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Anonymize\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class QuoteDataProcessor
 */
final class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Address
     */
    private $quoteAddressResourceModel;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface $anonymizer
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResourceModel
     */
    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository,
        Address $quoteAddressResourceModel
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteAddressResourceModel = $quoteAddressResourceModel;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(int $orderId): bool
    {
        try {
            $order = $this->orderRepository->get($orderId);

            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $this->quoteRepository->save($this->anonymizer->anonymize($quote));

            /** @var \Magento\Quote\Model\Quote\Address|null $quoteAddress */
            foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
                if ($quoteAddress) {
                    $this->quoteAddressResourceModel->save($this->anonymizer->anonymize($quoteAddress));
                }
            }
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
