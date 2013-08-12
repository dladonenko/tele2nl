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

$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color');
//$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('black' => array(0 => 'Black'))));
//$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('white' => array(0 => 'White'))));

$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('blue' => array(0 => 'Blue'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('green' => array(0 => 'Green'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('red' => array(0 => 'Red'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('purple' => array(0 => 'Purple'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('yellow' => array(0 => 'Yellow'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('pink' => array(0 => 'Pink'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('grey' => array(0 => 'Grey'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('magenta' => array(0 => 'Magenta'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('unknown' => array(0 => 'Unknown'))));

$installer->addAttributeToSet('catalog_product', 'accessory', 'General', 'color', 6);
$installer->addAttributeToSet('catalog_product', 'dongle', 'General', 'color', 6);

/*
    $installer->removeAttribute('catalog_product', 'color');
    $installer->addAttribute('catalog_product', 'color', array(
        'user_defined'               => true,
        'type'                       => 'int',
        'source'                     => 'eav/entity_attribute_source_table',
        'label'                      => 'Color',
        'required'                   => true,
        'input'                      => 'select',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'apply_to'                   => 'simple',
        'option' => array (
            'value' => array(
                'black' => array(0 => 'Black'),
                'white' => array(0 => 'White'),
                'purple' => array(0 => 'Purple'),
                'unknown' => array(0 => 'Unknown'),
                'blue' => array(0 => 'Blue'),
                'yellow' => array(0 => 'Yellow'),
                'pink' => array(0 => 'Pink'),
                'grey' => array(0 => 'Grey'),
                'green' => array(0 => 'Green'),
                'red' => array(0 => 'Red'),
                'magenta' => array(0 => 'Magenta'),
            ),
        )
    )
);
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'color', 5);*/

$installer->endSetup();