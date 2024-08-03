<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\Gdpr\Model\Config\Entity\Erasure as ErasureConfig;

class ExpireFilterModifier implements ModifierInterface
{
    public function __construct(
        private string $fieldToFilter,
        private ErasureConfig $erasureConfig
    ) {}

    /**
     * @throws Exception
     */
    public function apply(AbstractDb $collection, WebsiteInterface $website): void
    {
        $collection->addFieldToFilter(
            $this->fieldToFilter,
            ['lteq' => $this->erasureConfig->getEntityExpireDate($website->getId())]
        );
    }
}
