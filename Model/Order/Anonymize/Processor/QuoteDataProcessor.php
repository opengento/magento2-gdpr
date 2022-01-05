<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Anonymize\Processor;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class QuoteDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private OrderRepositoryInterface $orderRepository;

    private CartRepositoryInterface $quoteRepository;

    private Address $resourceModel;

    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository,
        Address $resourceModel
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        try {
            $this->processQuoteData($orderId);
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }

    /**
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    private function processQuoteData(int $orderId): void
    {
        $order = $this->orderRepository->get($orderId);

        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $this->quoteRepository->save($this->anonymizer->anonymize($quote));

        /** @var Quote\Address|null $quoteAddress */
        foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
            if ($quoteAddress) {
                $this->resourceModel->save($this->anonymizer->anonymize($quoteAddress));
            }
        }
    }
}
