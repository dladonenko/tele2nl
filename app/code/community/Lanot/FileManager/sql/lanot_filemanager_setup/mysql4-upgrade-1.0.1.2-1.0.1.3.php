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
    ->newTable($installer->getTable('lanot_file_storage'))
    ->addColumn('file_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'unsigned'  => true,
        'primary'   => true,
    ),  'File Id')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'length'    => 100,
        'nullable'  => false,
    ),  'Filename')
    ->addColumn('directory', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'length'    => 255,
        'default'   => null,
    ),  'Directory Path')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'length'    => 255,
        'default'   => null,
    ),  'File Type')
    ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ),  'Upload time')
    ->addColumn('last_modify_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ),  'Last modified time');

$installer->getConnection()->createTable($table);