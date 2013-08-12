<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/**
 * Create subscriptions table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_subscription'))
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
       ),  'Subscription Id')
  ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Subscription Name')
  ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price');

$installer->getConnection()->createTable($table);

/**
 * Create relations table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_relation'))
    ->addColumn('relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
       ),  'Subscription Id')
  ->addColumn('option_value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Option Value Id')
  ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Subscription Id')
  ->addColumn('binding_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Binding time')
  ->addForeignKey(
      $installer->getFkName('tele2_relation', 'option_value_id', 'catalog_product_option_type_value', 'option_type_id'),
      'option_value_id', 
      $installer->getTable('catalog_product_option_type_value'), 
      'option_type_id',
      Varien_Db_Ddl_Table::ACTION_CASCADE, 
      Varien_Db_Ddl_Table::ACTION_CASCADE
  );

$installer->getConnection()->createTable($table);
