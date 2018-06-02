<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:18
 */

namespace Flurrybox\EnhancedPrivacy\Service\Delete;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the delete processor for the given entity ID.
     * It allows to delete the related data.
     *
     * @param int $entityId
     * @return bool
     */
    public function execute(int $entityId): bool;
}
