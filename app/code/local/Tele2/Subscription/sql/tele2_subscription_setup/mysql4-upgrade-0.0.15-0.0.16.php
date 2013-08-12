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
$table = $installer->getTable('tele2_subscription');
$installer->getConnection()->addColumn(
    $table,
    'image',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Image',
    )
);