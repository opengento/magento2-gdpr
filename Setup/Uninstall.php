<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->deleteCustomAttributes();

        $setup->endSetup();
    }

    /**
     * Retrieve the module custom attributes codes by entity type
     *
     * @return array
     */
    private function getCustomAttributes(): array
    {
        return [
            Customer::ENTITY => [
                'is_anonymized',
            ],
        ];
    }

    /**
     * Delete the attributes
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface[] $attributes
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteAttributes(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            $this->attributeRepository->delete($attribute);
        }

        return true;
    }

    /**
     * Delete the custom attributes added by the module
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteCustomAttributes(): bool
    {
        foreach ($this->getCustomAttributes() as $entityType => $attributeCodes) {
            $this->searchCriteriaBuilder->addFilter(AttributeInterface::ATTRIBUTE_CODE, $attributeCodes);
            $attributes = $this->attributeRepository->getList($entityType, $this->searchCriteriaBuilder->create());

            $this->deleteAttributes($attributes->getItems());
        }

        return true;
    }
}
