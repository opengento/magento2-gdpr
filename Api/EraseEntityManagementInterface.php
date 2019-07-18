<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * Interface EraseEntityManagementInterface
 * @api
 */
interface EraseEntityManagementInterface
{
    /**
     * Initialize and save a new erase entity scheduler for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(int $entityId, string $entityType): EraseEntityInterface;

    /**
     * Cancel and remove erase entity scheduler for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @todo should not be responsible of the deletion (cancel != delete)
     */
    public function cancel(int $entityId, string $entityType): bool;

    /**
     * Run and process the erase entity scheduler command
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(EraseEntityInterface $entity): EraseEntityInterface;
}
