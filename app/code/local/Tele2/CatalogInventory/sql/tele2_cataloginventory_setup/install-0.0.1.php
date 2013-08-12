<?php
/**
 *
 * @category    Tele2
 * @package     Tele2_CatalogInventory
 */
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

if ($installer->getConnection()->isTableExists('comviq_virtualstock') && !$installer->getConnection()->isTableExists('tele2_cataloginventory/virtualstock')) {
    // Rename existen DB
    $installer->getConnection()->renameTable('comviq_virtualstock', $installer->getTable('tele2_cataloginventory/virtualstock'));
} elseif(!$installer->getConnection()->isTableExists('tele2_addon_relation')) {
    // Create table for virtual stock
    $installer->run("
        CREATE TABLE IF NOT EXISTS `{$installer->getTable('tele2_cataloginventory/virtualstock')}` (
            `virtualstock_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `product_id` int(10) unsigned NOT NULL,
            `level` tinyint(10) unsigned NOT NULL DEFAULT '1',
            `expected_date` date DEFAULT NULL,
            `amount` int(10) unsigned DEFAULT NULL,
            `left` int(10) unsigned DEFAULT NULL,
            PRIMARY KEY (`virtualstock_id`),
            KEY `product_id` (`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
} else {
    throw new Exception('Wrong Database Schema');
}

$installer->endSetup();