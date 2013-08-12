<?php
/**
 * Install script for Tele4G Catalog module
 * Add attribute 'color' to devices and make it configurable
 *
 * @category    Tele4G
 * @package     Tele4G_Catalog
 */
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * add Attribute Sets
 */
$attributeSets = array(
    'device',
    'subscription',
    'addon',
    'accessory',
);

$defaultSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityTypeId = $installer->getEntityTypeId('catalog_product');

foreach ($attributeSets as $attributeSetName) {
    if ($installer->getAttributeSet('catalog_product', $attributeSetName)) {
        continue;
    }

    $attributeSet = Mage::getModel('eav/entity_attribute_set');
    $attributeSet
        ->setEntityTypeId($entityTypeId)
        ->setAttributeSetName($attributeSetName);

    if ($attributeSet->validate()) {
        $attributeSet->save();
        $attributeSet->initFromSkeleton($defaultSetId);
        $attributeSet->save();
    }
}


/**
 * Update attribute 'color'
 */

$installer->updateAttribute('catalog_product', 'color', 'apply_to', 'simple');
$installer->updateAttribute('catalog_product', 'color', 'global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);
$installer->updateAttribute('catalog_product', 'color', 'input', 'select');

$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color');
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('black' => array(0 => 'Black'))));
$installer->addAttributeOption(array('attribute_id' => $attribute->getId(), 'value' => array('white' => array(0 => 'White'))));

$installer->addAttributeToSet('catalog_product', 'device', 'General', 'color', 6);



$installer->endSetup();
