<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * @api
 */
interface EraseEntityManagementInterface
{
    /**
     * Initialize and save a new erase entity scheduler for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return EraseEntityInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function create(int $entityId, string $entityType): EraseEntityInterface;

    /**
     * Cancel and remove erase entity scheduler for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     *
     * @todo should not be responsible of the deletion (cancel != delete)
     */
    public function cancel(int $entityId, string $entityType): bool;

    /**
     * Run and process the erase entity scheduler command
     *
     * @param EraseEntityInterface $entity
     * @return EraseEntityInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function process(EraseEntityInterface $entity): EraseEntityInterface;
}
