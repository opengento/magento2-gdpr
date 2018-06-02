<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:11
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class DeleteManagement
 */
class DeleteManagement
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
     * Delete all data related to a given entity ID
     *
     * @param int $entityId
     * @return bool
     */
    public function execute(int $entityId): bool
    {
        /** @var \Flurrybox\EnhancedPrivacy\Service\Delete\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            if (!$processor->execute($entityId)) {
                return false;
            }
        }

        return true;
    }
}
