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

$installer->addAttribute('catalog_product', 'new_product', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Mark as New product',
    'required'                   => false,
    'input'                      => 'boolean',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0,
));
$installer->addAttribute('catalog_product', 'hot_product', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Mark as Hot product',
    'required'                   => false,
    'input'                      => 'boolean',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0,
));

$installer->addAttributeToSet('catalog_product', 'accessory', 'General', 'new_product');
$installer->addAttributeToSet('catalog_product', 'addon', 'General', 'new_product');
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'new_product');
$installer->addAttributeToSet('catalog_product', 'dongle', 'General', 'new_product');
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'new_product');
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'new_product');

$installer->addAttributeToSet('catalog_product', 'accessory', 'General', 'hot_product');
$installer->addAttributeToSet('catalog_product', 'addon', 'General', 'hot_product');
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'hot_product');
$installer->addAttributeToSet('catalog_product', 'dongle', 'General', 'hot_product');
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'hot_product');
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'hot_product');