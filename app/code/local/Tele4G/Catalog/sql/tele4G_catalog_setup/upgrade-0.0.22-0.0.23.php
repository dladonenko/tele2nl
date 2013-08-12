<?php
/**
 *
 *
 * @category    Tele4G
 * @package     Tele4G_Catalog
 */

 /* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'addon_type');
$installer->removeAttribute('catalog_product', 'is_negative_price');

$installer->endSetup();