<?php
/**
 * Tele2 Subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/**
 * Create subscription configs table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_subscription_config'))
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),  'Subscription Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Subscription Config Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 0, array(), 'Subscription Config Description')
    ->addColumn('article_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(), 'Article ID')
    ->addColumn('priceplan', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(), 'Priceplan code (Partner ID)')
    ->addColumn('price_with_vat', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price with VAT')
    ->addColumn('price_without_vat', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price without VAT')
    ->addColumn('fee_with_vat', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Monthly fee with VAT')
    ->addColumn('fee_without_vat', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Monthly fee without VAT');

$installer->getConnection()->createTable($table);
