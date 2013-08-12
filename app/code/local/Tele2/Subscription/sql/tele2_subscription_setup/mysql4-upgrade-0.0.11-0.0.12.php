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
 *
 * @category    Comviq
 * @package     Comviq_CatalogInventory
 */
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists('comviq_rel_addon_rule') && !$installer->getConnection()->isTableExists('tele2_addon_relation')) {
    $installer->getConnection()->renameTable('comviq_rel_addon_rule', $installer->getTable('tele2_addon_relation'));
} elseif(!$installer->getConnection()->isTableExists('tele2_addon_relation')) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('tele2_subscription/addonrelation'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array('auto_increment' => true,'primary' => true,'nullable' => false,'unsigned' => true,), 'Rel id')
        ->addColumn('addon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array('nullable' => false,'unsigned' => true), 'Addon id')
        ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array('nullable' => true,'unsigned' => false,), 'Subscription id')
        ->addColumn('atype_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array('nullable' => true,'unsigned' => true,), 'Activation type id')
        ->addColumn('stype_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array('nullable' => true,'unsigned' => true,), 'Subscription Type 1 (1,2,3,4)')
        ->addIndex($installer->getIdxName('tele2_subscription/addonrelation', 'addon_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE), 'addon_id', array('type'=>Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName('tele2_subscription/addonrelation', 'subscription_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE), 'subscription_id', array('type'=>Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addForeignKey(
            $installer->getFkName($installer->getTable('tele2_subscription/addonrelation'), 'addon_id', $installer->getTable('catalog/product'), 'entity_id'),
            'addon_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName($installer->getTable('tele2_subscription/addonrelation'), 'subscription_id', $installer->getTable('tele2_subscription/subscription'), 'subscription_id'),
            'subscription_id',
            $installer->getTable('tele2_subscription/subscription'),
            'subscription_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE)        
        ->setComment('Rules for addon relations');
    $installer->getConnection()->createTable($table);
} else {
    throw new Exception('Wrong Database Schema');
}
$installer->endSetup();
