<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\ObjectManagerInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\ExportEntity;

/**
 * Class ExportEntityFactory
 * @api
 */
final class ExportEntityFactory
{
    /**
     * @var string[]
     */
    private $exportEntityList;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param array $exportEntityList
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $exportEntityList,
        ObjectManagerInterface $objectManager
    ) {
        $this->exportEntityList = $exportEntityList;
        $this->objectManager = $objectManager;
    }

    /**
     * Create export entity information
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\ExportEntityInterface
     */
    public function create(int $entityId): ExportEntityInterface
    {
        $exportEntity = new ExportEntity($entityId);

        foreach ($this->exportEntityList as $exportEntityItem) {
            $exportEntity = $this->objectManager->create($exportEntityItem, ['exportEntity' => $exportEntity]);
        }

        return $exportEntity;
    }
}
