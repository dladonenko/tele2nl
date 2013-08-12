<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

if (!$installer->getConnection()->isTableExists($installer->getTable('tele2_freegift'))) {
    //create table
    $table = $installer->getConnection()
        ->newTable($installer->getTable('tele2_freegift'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
           ),  'Free Gift Id')
      ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Free Gift Name')
      ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Coupon Code')
      ->addColumn('condition_subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Condition Subscription Id')
      ->addColumn('condition_binding_period', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Condition Binding Period')
      ->addColumn('condition_device_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Condition Device Id')
      ->addColumn('action_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Action Product Id');

    $installer->getConnection()->createTable($table);

}