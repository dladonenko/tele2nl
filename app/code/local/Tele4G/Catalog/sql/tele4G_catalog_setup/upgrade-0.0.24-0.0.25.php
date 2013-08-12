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

// create Attribute "monthly_price_without_vat" and attache their to AttributeSet "addon"
$installer->addAttribute('catalog_product', 'monthly_price_without_vat', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Monthly price without VAT',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0,
    'visible_on_front'           => false,
));
$installer->addAttributeToSet('catalog_product', 'addon', 'Prices', 'monthly_price_without_vat', 13);

// Remove logistics_article_id (duplicated by "partnerid")
$installer->removeAttribute('catalog_product', 'logistics_article_id');


$installer->endSetup();