<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:16
 */

namespace Flurrybox\EnhancedPrivacy\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class ExportManagement
 * @api
 */
class ExportManagement
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $processorPool;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $processorPool
     */
    public function __construct(
        TMap $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    /**
     * Export all data related to a given entity ID
     *
     * @param int $entityId
     * @return array
     */
    public function execute(int $entityId): array
    {
        $data = [];

        /** @var \Flurrybox\EnhancedPrivacy\Service\Export\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            $data = $processor->execute($entityId, $data);
        }

        return $data;
    }
}
