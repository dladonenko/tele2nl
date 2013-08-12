<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;


$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_abstract_subscription_attributes'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ),  'Entity Id')
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default' => null,
    ),  'Subscription Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Store Id')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price')
    ->addColumn('up_front_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'default' => null,
        'nullable' => true,
        'scale' => 2,
        'precision' => 5
    ), 'Up Front Price')
    ->addColumn('subsidy_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'default'   => null,
        'nullable'  => true,
        'scale'     => 2,
        'precision' => 5), 'Subsidy Price')
    ->addForeignKey(
        $installer->getFkName(
            $installer->getTable('tele2_abstract_subscription_attributes'),
            'subscription_id',
            $installer->getTable('tele2_abstract_subscription'),
            'subscription_id'),
        'subscription_id',
        $installer->getTable('tele2_abstract_subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            $installer->getTable('tele2_abstract_subscription_attributes'),
            'store_id',
            $installer->getTable('core_store'),
            'store_id'),
        'store_id',
        $installer->getTable('core_store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_mobile_subscription_attributes'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ),  'Entity Id')
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Subscription Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Store Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Subscription Name')
    ->addColumn('subtitle', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('length' => 100), 'Subscription Subtitle')
    ->addColumn('short_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription Short Description')
    ->addColumn('usp', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription USP')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription Description')
    ->addForeignKey(
        $installer->getFkName(
            $installer->getTable('tele2_mobile_subscription_attributes'),
            'subscription_id',
            $installer->getTable('tele2_mobile_subscription'),
            'subscription_id'),
        'subscription_id',
        $installer->getTable('tele2_mobile_subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            $installer->getTable('tele2_mobile_subscription_attributes'),
            'store_id',
            $installer->getTable('core_store'),
            'store_id'),
        'store_id',
        $installer->getTable('core_store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);

$installer->run("INSERT INTO tele2_abstract_subscription_attributes
                    (attribute_id, subscription_id, store_id, price, up_front_price, subsidy_price)
                SELECT
                    null, subscription_id, 1, price, up_front_price, subsidy_price
                FROM tele2_abstract_subscription");

$installer->run("INSERT INTO tele2_mobile_subscription_attributes
                    (attribute_id, subscription_id, store_id, name, subtitle, short_description, usp, description)
                SELECT
                    null, subscription_id, 1, name, subtitle, short_description, usp, description
                FROM tele2_mobile_subscription");

$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'price');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'articleid');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'up_front_price');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'subsidy_price');

$installer->getConnection()->dropColumn($installer->getTable('tele2_mobile_subscription'), 'name');
$installer->getConnection()->dropColumn($installer->getTable('tele2_mobile_subscription'), 'subtitle');
$installer->getConnection()->dropColumn($installer->getTable('tele2_mobile_subscription'), 'short_description');
$installer->getConnection()->dropColumn($installer->getTable('tele2_mobile_subscription'), 'usp');
$installer->getConnection()->dropColumn($installer->getTable('tele2_mobile_subscription'), 'description');
