<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface ExportGuestInterface
 * @api
 */
interface ExportGuestInterface
{
    /**
     * Export all data related to a given guest order entity to the file
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param string $fileName
     * @return string
     */
    public function exportToFile(OrderInterface $order, string $fileName): string;
}
