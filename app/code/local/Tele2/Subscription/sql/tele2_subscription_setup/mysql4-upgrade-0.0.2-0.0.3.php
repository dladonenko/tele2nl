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
 * Create binding period entity table
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_binding'))
    ->addColumn('binding_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
       ),  'Subscription Id')
  ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Subscription Id')
  ->addColumn('time', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Binding time')
  ->addColumn('billing_code', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Billing code')
  ->addColumn('invoice_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price')
  ->addForeignKey(
      $installer->getFkName('tele2_subscr', 'subscription_id', 'tele2_subscription', 'subscription_id'),
      'subscription_id', 
      $installer->getTable('tele2_subscription'), 
      'subscription_id',
      Varien_Db_Ddl_Table::ACTION_CASCADE, 
      Varien_Db_Ddl_Table::ACTION_CASCADE
  );
$installer->getConnection()->createTable($table);
