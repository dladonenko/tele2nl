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
 * Add month column and reneme Monthly fee to Monthly bonus
 */
$table = $installer->getTable('tele2_subscription_config');

$installer->run("ALTER TABLE {$table} CHANGE COLUMN `fee_with_vat` `fee_with_vat` decimal(5,2) NULL DEFAULT NULL COMMENT 'Monthly bonus with VAT';");
$installer->run("ALTER TABLE {$table} CHANGE COLUMN `fee_without_vat` `fee_without_vat` decimal(5,2) NULL DEFAULT NULL COMMENT 'Monthly bonus without VAT';");

$installer->getConnection()->addColumn(
    $table,
    'month',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length'    => 2,
        'comment'   => 'Month',
    )
);

