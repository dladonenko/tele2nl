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

$installer->removeAttribute('catalog_product', 'addon_group');

$installer->addAttribute('catalog_product', 'addon_group', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'Addon Group',
    'required'                   => false,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'option' => array (
        'value' => array(
            'addon_group_sms'      =>array(0=>'SMS-paket'),
            'addon_group_surfplis' =>array(0=>'Surfgrupp plus'),
            'addon_group_surfpaket'=>array(0=>'Surfpaket'),
            'addon_group_pluspaket'=>array(0=>'Pluspaket Mobilsurf'),
            'addon_group_surfgrupp'=>array(0=>'Surfgrupp'),
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'addon', 'General', 'addon_group', 3);

$installer->endSetup();