<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export\Notifier;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Export\NotifierFactory;

/**
 * Class ExportEntityManagement
 */
final class ExportEntityManagement implements ExportEntityManagementInterface
{
    /**
     * @var \Opengento\Gdpr\Api\ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @var \Opengento\Gdpr\Model\Export\NotifierFactory
     */
    private $exportNotifierFactory;

    /**
     * @param \Opengento\Gdpr\Api\ExportEntityManagementInterface $exportManagement
     * @param \Opengento\Gdpr\Model\Export\NotifierFactory $exportNotifierFactory
     */
    public function __construct(
        ExportEntityManagementInterface $exportManagement,
        NotifierFactory $exportNotifierFactory
    ) {
        $this->exportManagement = $exportManagement;
        $this->exportNotifierFactory = $exportNotifierFactory;
    }

    /**
     * @inheritdoc
     */
    public function export(ExportEntityInterface $exportEntity): string
    {
        $export = $this->exportManagement->export($exportEntity);
        $this->exportNotifierFactory->get('succeeded', $exportEntity->getEntityType())->notify($exportEntity);

        return $export;
    }
}
