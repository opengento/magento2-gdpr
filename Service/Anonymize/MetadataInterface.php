<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Anonymize;

use Opengento\Gdpr\Model\Entity\MetadataInterface as EntityMetadataInterface;

/**
 * Interface MetadataInterface
 * @api
 */
interface MetadataInterface extends EntityMetadataInterface
{
    /**
     * Retrieve the anonymizer strategies by attribute code
     *
     * @param string|null $scopeCode
     * @return array
     */
    public function getAnonymizerStrategiesByAttributes(?string $scopeCode = null): array;
}
