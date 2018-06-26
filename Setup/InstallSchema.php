<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        $this->createCronScheduleTable($setup);
        $this->createReasonTable($setup);

        $setup->endSetup();
    }

    /**
     * Create table 'opengento_gdpr_cleanup_schedule'
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return bool
     * @throws \Zend_Db_Exception
     */
    private function createCronScheduleTable(SchemaSetupInterface $setup): bool
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('opengento_gdpr_cleanup_schedule'))
            ->addColumn(
                'schedule_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id of schedule item'
            )
            ->addColumn(
                'scheduled_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Scheduled At'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Action type'
            )
            ->addColumn(
                'reason',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Reason text'
            )
            ->addIndex(
                $setup->getIdxName(
                    'opengento_gdpr_cleanup_schedule',
                    ['customer_id'],
                    true
                ),
                ['customer_id'],
                ['type' => 'unique']
            )
            ->setComment('Account Cleanup Schedule');

        $setup->getConnection()->createTable($table);

        return true;
    }

    /**
     * Create table 'opengento_gdpr_delete_reasons'
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return bool
     * @throws \Zend_Db_Exception
     */
    private function createReasonTable(SchemaSetupInterface $setup): bool
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('opengento_gdpr_delete_reasons'))
            ->addColumn(
                'reason_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Status id'
            )->addColumn(
                'reason',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Reason text'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->setComment('Comments statuses');

        $setup->getConnection()->createTable($table);

        return true;
    }
}
