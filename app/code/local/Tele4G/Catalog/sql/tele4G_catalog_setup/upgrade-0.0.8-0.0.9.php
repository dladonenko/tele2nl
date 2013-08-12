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

$installer->addAttribute('catalog_product', 'usp', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'USP',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'usp');

$installer->addAttribute('catalog_product', 'ss4_usp', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'USP',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_usp');

$installer->endSetup();