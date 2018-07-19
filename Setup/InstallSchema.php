<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer;

/**
 * Module install schema.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createEraseCustomerTable($setup);

        $setup->endSetup();
    }

    /**
     * Create table 'opengento_gdpr_erase_customer'
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return bool
     * @throws \Zend_Db_Exception
     */
    private function createEraseCustomerTable(SchemaSetupInterface $setup): bool
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(EraseCustomer::TABLE))
            ->addColumn(
                EraseCustomerInterface::ID,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                EraseCustomerInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                EraseCustomerInterface::SCHEDULED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Scheduled At'
            )
            ->addColumn(
                EraseCustomerInterface::STATE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'State'
            )
            ->addColumn(
                EraseCustomerInterface::STATUS,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                EraseCustomerInterface::ERASED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Erased At'
            )
            ->addIndex(
                $setup->getIdxName(
                    EraseCustomer::TABLE,
                    [EraseCustomerInterface::CUSTOMER_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [EraseCustomerInterface::CUSTOMER_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    EraseCustomer::TABLE,
                    EraseCustomerInterface::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                EraseCustomerInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_NO_ACTION
            )
            ->setComment('Customer Erase Scheduler');

        $setup->getConnection()->createTable($table);

        return true;
    }
}
