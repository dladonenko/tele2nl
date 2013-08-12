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
 * Add a column at subscription relation table
 */

$table = $installer->getConnection()->addColumn(
    $installer->getTable('tele2_relation'),
    'product_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'default'   => null,
        'nullable'  => true,
        'comment'   => 'Product Id'
    )
);

