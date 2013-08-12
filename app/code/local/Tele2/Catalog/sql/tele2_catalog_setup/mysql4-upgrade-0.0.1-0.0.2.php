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

$installer->addAttribute('catalog_product', 'splash', array(
    'label'                      => 'Splash Image',
    'input'                      => 'select',
    'source'                     => 'tele2_catalog/product_attribute_backend_splash',
    'sort_order'                 => 100,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'                   => false,
    'group'                      => 'General',
));
