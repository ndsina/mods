<?php
namespace GoMage\PostCode\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $setup->startSetup();

            $table = $setup->getConnection()->newTable(
                $setup->getTable('gomage_postcode')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Record Id'
            )->addColumn(
                'zip_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['unsigned' => true, 'nullable' => false],
                'Zip Code'
            )->addColumn(
                'encoded_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Data'
            )->setComment(
                'Post codes'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('gomage_postcode'),
                    ['zip_code'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                'zip_code',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );
            
            $setup->getConnection()->createTable($table);

            $setup->endSetup();
        }
    }
}