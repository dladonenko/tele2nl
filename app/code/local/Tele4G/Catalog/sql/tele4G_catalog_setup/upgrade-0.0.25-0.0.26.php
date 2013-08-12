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

$installer->addAttribute('catalog_product', 'togo_product', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Mark as ToGo product',
    'required'                   => false,
    'input'                      => 'boolean',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0,
));

$installer->addAttributeToSet('catalog_product', 'accessory', 'General', 'togo_product');
$installer->addAttributeToSet('catalog_product', 'addon', 'General', 'togo_product');
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'togo_product');
$installer->addAttributeToSet('catalog_product', 'dongle', 'General', 'togo_product');
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'togo_product');
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'togo_product');
