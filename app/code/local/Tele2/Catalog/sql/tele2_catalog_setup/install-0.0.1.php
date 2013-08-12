<?php
/**
 *
 *
 * @category    Tele2
 * @package     Tele2_Catalog
 */

 /* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add attribute 'redirect_url' for category
 */
$installer->removeAttribute('catalog_category', 'redirect_url');

$installer->addAttribute('catalog_category', 'redirect_url', array(
    'type'                       => 'varchar',
    'label'                      => 'Redirect Url',
    'input'                      => 'text',
    'sort_order'                 => 100,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'                   => false,
    'group'                      => 'General Information',
));

