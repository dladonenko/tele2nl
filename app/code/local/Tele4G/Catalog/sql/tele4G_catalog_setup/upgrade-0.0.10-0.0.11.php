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

$installer->removeAttribute('catalog_product', 'usp');
$installer->removeAttribute('catalog_product', 'ss4_usp');

$installer->addAttribute('catalog_product', 'usp', array(
    'user_defined'               => true,
    'type'                       => 'text',
    'label'                      => 'USP',
    'required'                   => false,
    'input'                      => 'textarea',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
    'wysiwyg_enabled'            => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'usp');

$installer->addAttribute('catalog_product', 'ss4_usp', array(
    'user_defined'               => true,
    'type'                       => 'text',
    'label'                      => 'USP',
    'required'                   => false,
    'input'                      => 'textarea',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => true,
    'wysiwyg_enabled'         => true,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_usp');

$installer->endSetup();