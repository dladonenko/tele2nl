<?php
/**
 * Lanot FileManager module
 *
 * @category    Lanot
 * @package     Lanot_FileManager
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$table = $installer->getConnection()
    ->newTable($installer->getTable('lanot_file_product'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'unsigned'  => true,
        'primary'   => true,
    ),  'File Id')
    ->addColumn('file_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Filename')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Directory Path')
    ->addForeignKey(
        $installer->getFkName(
            $installer->getTable('lanot_file_product'),
            'file_id',
            $installer->getTable('lanot_file_storage'),
            'file_id'),
        'file_id',
        $installer->getTable('lanot_file_storage'),
        'file_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            $installer->getTable('lanot_file_product'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);