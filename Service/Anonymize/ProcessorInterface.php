<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:18
 */

namespace Flurrybox\EnhancedPrivacy\Service\Anonymize;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the anonymize processor for the given entity ID.
     * It allows to anonymize the related data.
     *
     * @param int $entityId
     * @return bool
     */
    public function execute(int $entityId): bool;
}
