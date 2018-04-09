<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   Open Software License ('OSL') v. 3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Module install data schema.
 */
class InstallData implements InstallDataInterface
{
    const IS_ANONYMIZED_ATTRIBUTE = 'is_anonymized';

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Installs DB schema for a module.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->removeAttribute(Customer::ENTITY, self::IS_ANONYMIZED_ATTRIBUTE);

        $customerSetup->addAttribute(Customer::ENTITY, self::IS_ANONYMIZED_ATTRIBUTE, array(
            'type' => 'int',
            'label' => 'Is Anonimyzed',
            'input' => 'select',
            'source' => Boolean::class,
            'visible' => true,
            'default' => 0,
            'required' => false,
        ));


        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, self::IS_ANONYMIZED_ATTRIBUTE);

        $attribute
            ->setData('used_in_forms', [
                'adminhtml_customer',
                'checkout_register',
                'customer_account_create',
                'customer_account_edit',
                'adminhtml_checkout'
            ])
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', 0)
            ->setData('is_user_defined', 1)
            ->setData('is_visible', 1)
            ->setData('sort_order', 100);

        $attribute->getResource()->save($attribute);

        $installer->endSetup();
    }
}
