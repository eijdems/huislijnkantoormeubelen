<?php
namespace Emizen\CheckoutCustomField\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            // Add reference_number field to the quote table
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'reference_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Reference Number'
                ]
            );

            // Add custom_file field to the quote table
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'custom_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Uploaded File Name'
                ]
            );

            // Add reference_number field to the sales_order table
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'reference_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Reference Number'
                ]
            );

            // Add custom_file field to the sales_order table
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'custom_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Uploaded File Name'
                ]
            );
        }

        $installer->endSetup();
    }
}
