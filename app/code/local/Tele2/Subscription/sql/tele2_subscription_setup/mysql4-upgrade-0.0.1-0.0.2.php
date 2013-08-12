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
$table = $installer->getConnection()->addColumn(
    $installer->getTable('tele2_subscription'), 
    'fake_product_id', 
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => true,
        'comment'   => 'Fake Product Id'
    )
);
