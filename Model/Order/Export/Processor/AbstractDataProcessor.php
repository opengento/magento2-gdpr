<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export\Processor;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor as AbstractExportDataProcessor;

abstract class AbstractDataProcessor extends AbstractExportDataProcessor
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($dataCollector);
    }

    public function execute(int $entityId, array $data): array
    {
        return $this->export($this->orderRepository->get($entityId), $data);
    }

    /**
     * Execute the export processor for the given order entity.
     * It allows to retrieve the related data as an array.
     *
     * @param OrderInterface $order
     * @param array $ata
     * @return array
     */
    abstract protected function export(OrderInterface $order, array $ata): array;
}
