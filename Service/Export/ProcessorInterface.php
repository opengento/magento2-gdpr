<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/06/18
 * Time: 17:18
 */

namespace Flurrybox\EnhancedPrivacy\Service\Export;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the export processor for the given entity ID.
     * It allows to retrieve the related data as an array.
     *
     * @param int $entityId
     * @param array $data
     * @return array
     */
    public function execute(int $entityId, array $data): array;
}
