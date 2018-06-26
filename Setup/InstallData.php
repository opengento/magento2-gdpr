<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute
     */
    private $attributeRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        Attribute $attributeRepository,
        Config $eavConfig
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeRepository = $attributeRepository;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $this->addIsAnonymizedAttribute($customerSetup);

        $setup->endSetup();
    }

    /**
     * Add new customer attribute 'is_anonymized'
     *
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return bool
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addIsAnonymizedAttribute(CustomerSetup $customerSetup): bool
    {
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'is_anonymized',
            [
                'label' => 'Is Anonymized',
                'type' => 'int',
                'input' => 'select',
                'source' => Boolean::class,
                'input_filter' => '',
                'default' => 0,
                'required' => false,
                'visible' => true,
                'user_defined' => false,
                'system' => false,
                'position' => 85,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
            ]
        );

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'is_anonymized');
        $attribute->setData(
            'used_in_forms',
            [
                'adminhtml_customer',
                'checkout_register',
                'customer_account_create',
                'customer_account_edit',
                'adminhtml_checkout',
            ]
        );

        $this->attributeRepository->save($attribute);

        return true;
    }
}
