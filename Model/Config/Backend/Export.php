<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Backend;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;

class Export extends Value
{
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource,
        AbstractDb $resourceCollection,
        private ExportEntityRepositoryInterface $exportRepository,
        private SearchCriteriaBuilder $criteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function afterSave(): self
    {
        if ($this->isValueChanged()) {
            foreach ($this->exportRepository->getList($this->criteriaBuilder->create())->getItems() as $exportEntity) {
                $this->exportRepository->delete($exportEntity);
            }
        }

        return parent::afterSave();
    }
}
