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
 * Add a few columns at subscription table
 */

$columns = array (
    'subtitle' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 100,
        'comment'   => 'Subscription Subtitle'
    ),
    'short_description' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'   => 'Subscription Short Description'
    ),
    'usp' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'   => 'Subscription USP'
    ),
    'description' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'   => 'Subscription Description'
    ),
    'up_front_price' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'default'   => null,
        'nullable'  => true,
        'scale'     => 2,
        'precision' => 5,
        'comment'   => 'Up Front Price'
    ),
    'type1' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'default'   => 1,
        'unsigned'  => true,
        'comment'   => 'Type1: Pre-Post'
    ),
    'standalone' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'default'   => 0,
        'unsigned'  => true,
        'comment'   => 'Standalone'
    ),
    'type2' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'default'   => 1,
        'unsigned'  => true,
        'comment'   => 'Type2: S, M, L'
    ),
    'downgrade' => array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'default'   => 1,
        'unsigned'  => true,
        'comment'   => 'Type2: NA, GN0, GN1'
    ),
);

foreach ($columns as $columnName => $columnData) {
    $table = $installer->getConnection()->addColumn(
        $installer->getTable('tele2_subscription'),
        $columnName,
        $columnData
    );
}
