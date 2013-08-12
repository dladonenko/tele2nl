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
 * Add a few columns at subscription config table
 */

$columns = array (
    'image_main' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Image'
    ),
    'display_in_cart' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length'    => 1,
        'comment'   => 'Display in cart'
    ),
);

foreach ($columns as $columnName => $columnData) {
    $table = $installer->getConnection()->addColumn(
        $installer->getTable('tele2_subscription_config'),
        $columnName,
        $columnData
    );
}
