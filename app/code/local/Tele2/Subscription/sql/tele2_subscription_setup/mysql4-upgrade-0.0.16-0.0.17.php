<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$fkName = $installer->getConnection()->getForeignKeyName(
        $installer->getTable('tele2_binding'),
        'subscription_id',
        $installer->getTable('tele2_subscription'),
        'subscription_id'
        );
$installer->getConnection()->dropKey('tele2_binding', $fkName);

$installer->run('RENAME TABLE tele2_subscription TO tele2_abstract_subscription;');

$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_mobile_subscription'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ),  'Entity Id')
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ),  'Subscription Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Subscription Name')
    ->addColumn('articleid', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'length' => 100,
        'nullable'  => true,
        'default'  => null
    ), 'Subscription ID in SS4')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array('scale' => 2, 'precision' => 5), 'Price')
    ->addColumn('subtitle', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('length' => 100), 'Subscription Subtitle')
    ->addColumn('short_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription Short Description')
    ->addColumn('usp', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription USP')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Subscription Description')
    ->addColumn('type1', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default' => 1,
        'unsigned' => true
    ), 'Type1: Pre-Post')
    ->addColumn('type2', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default' => 1,
        'unsigned' => true
    ), 'Type2: S, M, L')
    ->addColumn('downgrade', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => 1,
        'unsigned'  => true
    ), 'Type2: NA, GN0, GN1')
    ->addColumn('priceplan', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('length' => 3), 'Priceplan')
    ->addColumn('subscription_group', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'default' => 1), 'Subscription Group')
    ->addForeignKey(
        $installer->getFkName(
            $installer->getTable('tele2_mobile_subscription'),
            'subscription_id',
            $installer->getTable('tele2_abstract_subscription'),
            'subscription_id'),
        'subscription_id',
        $installer->getTable('tele2_abstract_subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);

$installer->run("INSERT INTO tele2_mobile_subscription
                    (entity_id, subscription_id, name, articleid, subtitle, short_description, usp, description, type1,
                    type2, downgrade, priceplan, subscription_group)
                SELECT
                    null, subscription_id, name, articleid, subtitle, short_description, usp,
                    description, type1, type2, downgrade, priceplan, subscription_group
                FROM tele2_abstract_subscription");

$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'name');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'articleid');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'subtitle');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'short_description');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'usp');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'description');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'type1');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'type2');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'downgrade');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'priceplan');
$installer->getConnection()->dropColumn($installer->getTable('tele2_abstract_subscription'), 'subscription_group');
