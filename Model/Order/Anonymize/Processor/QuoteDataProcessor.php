<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Anonymize\Processor;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Address
     */
    private $resourceModel;

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
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
