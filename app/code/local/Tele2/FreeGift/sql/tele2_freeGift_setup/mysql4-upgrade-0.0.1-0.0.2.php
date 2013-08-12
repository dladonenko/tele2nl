<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->dropColumn($installer->getTable('tele2_freegift'), 'condition_binding_period');
$installer->getConnection()->addColumn(
    $installer->getTable('tele2_freegift'),
    'condition_binding_period',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length'   => 100,
        'comment'  => 'Device to add'
    )
);

$installer->getConnection()
    ->dropColumn($installer->getTable('tele2_freegift'), 'action_product_id');
$installer->getConnection()->addColumn(
    $installer->getTable('tele2_freegift'),
    'action_product_id',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length'   => 254,
        'comment'  => 'Gift to add'
    )
);
