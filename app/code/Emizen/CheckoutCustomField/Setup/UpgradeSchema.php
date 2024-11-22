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

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
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

            // Add reference_number field to the sales_order table
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'reference_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Reference Number'
                ]
            );
        }

        $installer->endSetup();
    }
}
