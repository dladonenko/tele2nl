<?php
/**
 *
 *
 * @category    Tele2
 * @package     Tele2_Catalog
 */

 /* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add attribute 'splash' for product
 */

$installer->updateAttribute('catalog_product', 'splash', 'used_in_product_listing', true);
