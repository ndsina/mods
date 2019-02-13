<?php

namespace GoMage\SmsNotifications\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('gomage_sms'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Sms id'
            )
            ->addColumn(
                'batch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [
                    'default' => '',
                    'nullable' => false
                ],
                'CLX Batch ID'
            )
            ->addColumn(
                'from',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Sender'
            )
            ->addColumn(
                'phone_list',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'List of recipients numbers'
            )
            ->addColumn(
                'message_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Message'
            )
            ->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Product name'
            )
            ->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Product SKU'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [
                    'nullable' => false,
                    'default' => '0.0000'
                ],
                'Product Price'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                ],
                'SMS Creation Time'
            )
            ->addColumn(
                'expired_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                ],
                'Expired Time'
            )
            ->addIndex(
                $installer->getIdxName(
                    'gomage_sms',
                    ['batch_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['batch_id'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ]
            )
            ->addIndex(
                $installer->getIdxName(
                    $installer->getTable('gomage_sms'),
                    ['phone_list'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['phone_list'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ]
            )
            ->addIndex(
                $installer->getIdxName(
                    $installer->getTable('gomage_sms'),
                    ['from'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['from'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ]
            )
            ->addIndex(
                $installer->getIdxName(
                    $installer->getTable('gomage_sms'),
                    ['product_name'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['product_name'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ]
            )
            ->addIndex(
                $installer->getIdxName(
                    $installer->getTable('gomage_sms'),
                    ['product_sku'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['product_sku'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ]
            )
            ->addIndex(
                $installer->getIdxName(
                    $installer->getTable('gomage_sms'),
                    ['created_at'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['created_at'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ]
            )
            ->setComment('GoMage Sms');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
