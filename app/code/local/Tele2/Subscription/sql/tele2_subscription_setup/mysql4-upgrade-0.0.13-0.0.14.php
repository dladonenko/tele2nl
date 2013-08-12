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
 * Create subscription configs relation table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tele2_subscription/configrelation'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('auto_increment' => true,'primary' => true,'nullable' => false), 'Rel id')
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false), 'Subscription Config id')
    ->addColumn('subscription_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false), 'Subscription Entity id')
    ->addIndex($installer->getIdxName('tele2_subscription/configrelation', 'config_id'), 'config_id')
    ->addIndex($installer->getIdxName('tele2_subscription/configrelation', 'subscription_entity_id'), 'subscription_entity_id')
    ->addForeignKey(
        $installer->getFkName('tele2_subscription/configrelation', 'config_id', 'tele2_subscription/subscription_config', 'config_id'),
        'config_id',
        $installer->getTable('tele2_subscription/subscription_config'),
        'config_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tele2_subscription/configrelation', 'subscription_entity_id', 'tele2_subscription/subscription', 'subscription_id'),
        'subscription_entity_id',
        $installer->getTable('tele2_subscription/subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Rules for Subscription Config relations');

$installer->getConnection()->createTable($table);

$installer->endSetup();
