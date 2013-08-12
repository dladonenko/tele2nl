<?php
/**
 * Install script for Tele4G Catalog module
 * Add attribute 'color' to devices and make it configurable
 *
 * @category    Tele4G
 * @package     Tele4G_Catalog
 */
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'sim_type', 'apply_to');

$installer->endSetup();
