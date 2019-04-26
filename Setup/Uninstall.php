<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer;

/**
 * Class Uninstall
 */
final class Uninstall implements UninstallInterface
{
    /**
     * @inheritdoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $this->deleteTables($setup->getConnection());

        $setup->endSetup();
    }

    /**
     * Drop the tables added by the module
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @return bool
     */
    private function deleteTables(AdapterInterface $connection): bool
    {
        $connection->dropTable(EraseCustomer::TABLE);

        return true;
    }
}
