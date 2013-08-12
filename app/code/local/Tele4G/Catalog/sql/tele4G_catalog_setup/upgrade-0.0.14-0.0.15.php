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
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('silver' => array(0 => 'Silver'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('brown' => array(0 => 'Brown'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('gold' => array(0 => 'Gold'))));

$installer->endSetup();