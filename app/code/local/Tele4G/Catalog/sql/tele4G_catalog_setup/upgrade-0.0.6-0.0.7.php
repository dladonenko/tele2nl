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

$installer->removeAttribute('catalog_product', 'ss4_warranty2years');
$installer->removeAttribute('catalog_product', 'warranty2years');

$installer->addAttribute('catalog_product', 'warranty', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Garantitid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'warranty');

$installer->addAttribute('catalog_product', 'ss4_warranty', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Garantitid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_warranty');

$installer->addAttribute('catalog_product', 'android', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Android',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'android');

$installer->addAttribute('catalog_product', 'ss4_android', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Android',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_android');

$installer->endSetup();