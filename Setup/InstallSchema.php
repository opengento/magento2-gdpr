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
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Setup;

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
     * Installs DB schema for a module.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'flurrybox_enhancedprivacy_delete_reasons'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('flurrybox_enhancedprivacy_delete_reasons'))
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

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'flurrybox_enhancedprivacy_cleanup_schedule'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('flurrybox_enhancedprivacy_cleanup_schedule'))
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
                    'flurrybox_enhancedprivacy_cleanup_schedule',
                    ['customer_id'],
                    true
                ),
                ['customer_id'],
                ['type' => 'unique']
            )
            ->setComment('Account Cleanup Schedule');

        $installer->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
