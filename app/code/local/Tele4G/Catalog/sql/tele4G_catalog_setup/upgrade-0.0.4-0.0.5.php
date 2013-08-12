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

/**
 * Add Attributes for dongle
 */
$installer->addAttributeGroup('catalog_product', 'dongle', 'Specifications', 2);
$installer->addAttributeGroup('catalog_product', 'dongle', 'SS4', 3);

$installer->addAttribute('catalog_product', 'frequency_2g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Frequency 2G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'frequency_2g', 1);
$installer->addAttribute('catalog_product', 'ss4_frequency_2g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Frequency 2G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_frequency_2g', 1);

$installer->addAttribute('catalog_product', 'frequency_3g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Frequency 3G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'frequency_3g', 2);
$installer->addAttribute('catalog_product', 'ss4_frequency_3g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Frequency 3G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_frequency_3g', 2);

$installer->addAttribute('catalog_product', 'frequency_4g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Frequency 4G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'frequency_4g', 3);
$installer->addAttribute('catalog_product', 'ss4_frequency_4g', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Frequency 4G',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_frequency_4g', 3);

$installer->addAttribute('catalog_product', 'hard_drive_space', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Hard drive space',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'hard_drive_space', 4);
$installer->addAttribute('catalog_product', 'ss4_hard_drive_space', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Hard drive space',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_hard_drive_space', 4);

$installer->addAttribute('catalog_product', 'modem', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Modem',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'modem', 5);
$installer->addAttribute('catalog_product', 'ss4_modem', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Modem',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_modem', 5);

$installer->addAttribute('catalog_product', 'download_speed', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Download speed',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'download_speed', 6);
$installer->addAttribute('catalog_product', 'ss4_download_speed', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Download speed',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_download_speed', 6);

$installer->addAttribute('catalog_product', 'operating_systems', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Operating systems',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'operating_systems', 7);
$installer->addAttribute('catalog_product', 'ss4_operating_systems', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Operating systems',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_operating_systems', 7);

$installer->addAttribute('catalog_product', 'port', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Port',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'port', 8);
$installer->addAttribute('catalog_product', 'ss4_port', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Port',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_port', 8);

$installer->addAttribute('catalog_product', 'ram', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'RAM',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'ram', 9);
$installer->addAttribute('catalog_product', 'ss4_ram', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 RAM',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_ram', 9);

$installer->addAttribute('catalog_product', 'screen_resolutions', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Screen resolutions',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'screen_resolutions', 10);
$installer->addAttribute('catalog_product', 'ss4_screen_resolutions', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Screen resolutions',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_screen_resolutions', 10);

$installer->addAttribute('catalog_product', 'upload_speed', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Upload speed',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'upload_speed', 11);
$installer->addAttribute('catalog_product', 'ss4_upload_speed', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Upload speed',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_upload_speed', 11);

$installer->addAttribute('catalog_product', 'outgoing_calls', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Outgoing calls',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'outgoing_calls', 12);
$installer->addAttribute('catalog_product', 'ss4_outgoing_calls', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Outgoing calls',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_outgoing_calls', 12);

$installer->addAttribute('catalog_product', 'outgoing_sms', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Outgoing SMS',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'outgoing_sms', 13);
$installer->addAttribute('catalog_product', 'ss4_outgoing_sms', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Outgoing SMS',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_outgoing_sms', 13);

$installer->addAttribute('catalog_product', 'surf_abroad_roaming', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'Surf abroad (roaming)',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'Specifications', 'surf_abroad_roaming', 14);
$installer->addAttribute('catalog_product', 'ss4_surf_abroad_roaming', array(
    'user_defined'  => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Surf abroad (roaming)',
    'input'                      => 'text',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_surf_abroad_roaming', 14);

$installer->endSetup();