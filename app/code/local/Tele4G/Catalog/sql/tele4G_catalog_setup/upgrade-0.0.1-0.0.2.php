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
 * Add attribute 'category_code' for category
 */
$installer->addAttribute('catalog_category', 'code', array(
    'type'                       => 'varchar',
    'label'                      => 'Category Code',
    'input'                      => 'text',
    'sort_order'                 => 3,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'                   => true,
    'group'                      => 'General Information',
));


/**
 * Add attributes (SS4)
 */
$installer->addAttributeGroup('catalog_product', 'device', 'SS4', 2);

$installer->addAttribute('catalog_product', 'variant_master', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Master Variant',
    'required'                   => false,
    'input'                      => 'boolean',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0,
));
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'variant_master', 1);

$installer->addAttribute('catalog_product', 'override_name', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Name override',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->updateAttribute('catalog_product', 'override_name', 'used_in_product_listing', true);
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'override_name', 1);

$installer->addAttribute('catalog_product', 'override_subtitle', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Subtitle override',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'override_subtitle', 2);

$installer->addAttribute('catalog_product', 'override_description', array(
    'user_defined'  => true,
    'type'                       => 'text',
    'label'                      => 'Description override',
    'input'                      => 'textarea',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'override_description', 2);

$installer->addAttribute('catalog_product', 'override_short_description', array(
    'user_defined'  => true,
    'type'                       => 'text',
    'label'                      => 'Short Description override',
    'input'                      => 'textarea',
    'required'                   => false,
    'sort_order'                 => 3,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'override_short_description', 3);

$installer->addAttribute('catalog_product', 'monthly_price', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'backend'                    => 'catalog/product_attribute_backend_price',
    'label'                      => 'Monthly Price',
    'required'                   => true,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->updateAttribute('catalog_product', 'monthly_price', 'used_in_product_listing', true);
$installer->addAttributeToSet('catalog_product', 'subscription', 'Prices', 'monthly_price', 3);

$installer->addAttribute('catalog_product', 'subsidy_price', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'backend'                    => 'catalog/product_attribute_backend_price',
    'label'                      => 'Subsidy Price',
    'required'                   => true,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Prices', 'subsidy_price', 3);

$installer->addAttribute('catalog_product', 'bind_period', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'Binding Period',
    'required'                   => false,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'option' => array (
        'value' => array(
            'bind_period_test12'=>array(0=>'12'),
            'bind_period_test24'=>array(0=>'24'),
            'bind_period_test36'=>array(0=>'36'),
        ),
    ),
));

$installer->addAttribute('catalog_product', 'subscription_type', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'Type',
    'required'                   => true,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'option' => array (
        'value' => array(
            'post'=>array(0=>'post'),
            'pre'=>array(0=>'pre'),
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'subscription_type', 5);

$installer->addAttribute('catalog_product', 'subscription_type2', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'Type 2',
    'required'                   => true,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'option' => array (
        'value' => array(
            'mobile'=>array(0=>'mobile'),
            'mbb'=>array(0=>'mbb'),
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'subscription_type2', 5);

$installer->addAttribute('catalog_product', 'standalone', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_boolean',
    'label'                      => 'Standalone',
    'required'                   => true,
    'input'                      => 'boolean',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'                    => 0
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'standalone', 5);

$installer->addAttribute('catalog_product', 'subscription_group', array(
    'user_defined'               => true,
    'type'                       => 'int',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'Group',
    'required'                   => true,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'option' => array (
        'value' => array(
            'small'=>array(0=>'small'),
            'medium'=>array(0=>'medium'),
            'large'=>array(0=>'large'),
            'simOnly'=>array(0=>'sim Only'),
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'subscription_group', 5);

$installer->updateAttribute('catalog_product', 'color', 'required', 1);

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
            'addon_group_internet'=>array(0=>'Internet'),
            'addon_group_SMS'=>array(0=>'SMS'),
            'addon_group_MMS'=>array(0=>'MMS'),
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'addon', 'General', 'addon_group', 3);

$installer->addAttribute('catalog_product', 'ss4_id', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 item Id',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_partnerid', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Partner Id',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_make', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Make',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_name', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Item name',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_description', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Item description',
    'required'                   => false,
    'input'                      => 'textarea',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_shortdescription', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Item short description',
    'required'                   => false,
    'input'                      => 'textarea',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_pricewithoutvat', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Price without VAT',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_pricewithvat', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Price with VAT',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_color', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Color',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'SS4',
));

$installer->addAttribute('catalog_product', 'ss4_autofocus', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Autofokus',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_autofocus');

$installer->addAttribute('catalog_product', 'ss4_rearcamera', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Bakåtriktad kamera',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_rearcamera');

$installer->addAttribute('catalog_product', 'ss4_batterycapacity', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Batterikapacitet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_batterycapacity');

$installer->addAttribute('catalog_product', 'ss4_bluetooth', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Blåtand',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_bluetooth');

$installer->addAttribute('catalog_product', 'ss4_bluetoothversion', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Blåtandsversion',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_bluetoothversion');

$installer->addAttribute('catalog_product', 'ss4_chipset', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Chipset',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_chipset');

$installer->addAttribute('catalog_product', 'ss4_datarate', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Datahastighet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_datarate');

$installer->addAttribute('catalog_product', 'ss4_sharpestcameraphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'De vassaste kameratelefonerna',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_sharpestcameraphone');

$installer->addAttribute('catalog_product', 'ss4_latestandhottest', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Det senaste och hetaste',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_latestandhottest');

$installer->addAttribute('catalog_product', 'ss4_dimensions', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dimensioner',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_dimensions');

$installer->addAttribute('catalog_product', 'ss4_dlna', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'DLNA',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_dlna');

$installer->addAttribute('catalog_product', 'ss4_dualcoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dual core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_dualcoreprocessor');

$installer->addAttribute('catalog_product', 'ss4_dualcoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dual core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_dualcoreprocessor');

$installer->addAttribute('catalog_product', 'ss4_simplephonewithbuttons', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'En enkel telefon med knappar',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_simplephonewithbuttons');

$installer->addAttribute('catalog_product', 'ss4_email', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'E-post',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_email');

$installer->addAttribute('catalog_product', 'ss4_flightmode', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Flight mode',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_flightmode');

$installer->addAttribute('catalog_product', 'ss4_fmradio', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'FM-radio',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_fmradio');

$installer->addAttribute('catalog_product', 'ss4_frontcamera', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Framåtriktad kamera',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_frontcamera');

$installer->addAttribute('catalog_product', 'ss4_warranty2years', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Garantitid 2 år',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_warranty2years');

$installer->addAttribute('catalog_product', 'ss4_gps', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'GPS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_gps');

$installer->addAttribute('catalog_product', 'ss4_hdmi', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'HDMI',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_hdmi');

$installer->addAttribute('catalog_product', 'ss4_ios', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'IOS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_ios');

$installer->addAttribute('catalog_product', 'ss4_cameraover5mpix', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kamera över 5 Mpix',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_cameraover5mpix');

$installer->addAttribute('catalog_product', 'ss4_cameraflash', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kamerablixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_cameraflash');

$installer->addAttribute('catalog_product', 'ss4_rearcameraresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kameraupplösning (bak)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_rearcameraresolution');

$installer->addAttribute('catalog_product', 'ss4_frontcameraresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kameraupplösning (fram)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_frontcameraresolution');

$installer->addAttribute('catalog_product', 'ss4_classickeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Klassisk knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_classickeypad');

$installer->addAttribute('catalog_product', 'ss4_mediatypessupported', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Mediatyper som stödjs',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_mediatypessupported');

$installer->addAttribute('catalog_product', 'ss4_memorycardslot', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Minneskortsplats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_memorycardslot');

$installer->addAttribute('catalog_product', 'ss4_memorycardtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Minneskorttyp',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_memorycardtype');

$installer->addAttribute('catalog_product', 'ss4_mms', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_mms');

$installer->addAttribute('catalog_product', 'ss4_mp3player', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MP3-spelare',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_mp3player');

$installer->addAttribute('catalog_product', 'ss4_navigationgpssoftincluded', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Navigationsmjukvara för GPS ingår',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_navigationgpssoftincluded');

$installer->addAttribute('catalog_product', 'ss4_networkbands2g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 2G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_networkbands2g');

$installer->addAttribute('catalog_product', 'ss4_networkbands3g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 3G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_networkbands3g');

$installer->addAttribute('catalog_product', 'ss4_networkbands4g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 4G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_networkbands4g');

$installer->addAttribute('catalog_product', 'ss4_operatingsystemversion', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Operativsystemversion',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_operatingsystemversion');

$installer->addAttribute('catalog_product', 'ss4_operatorlocked', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Operatörslåst',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_operatorlocked');

$installer->addAttribute('catalog_product', 'ss4_touchscreen', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Pekskärm',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_touchscreen');

$installer->addAttribute('catalog_product', 'ss4_processorspeed', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Processorhastighet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_processorspeed');

$installer->addAttribute('catalog_product', 'ss4_qwertykeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'QWERTY knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_qwertykeypad');

$installer->addAttribute('catalog_product', 'ss4_qwertykeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'QWERTY knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_qwertykeypad');

$installer->addAttribute('catalog_product', 'ss4_ram', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'RAM-minne (arbetsminne)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_ram');

$installer->addAttribute('catalog_product', 'ss4_rom', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'ROM-minne (lagringsminne)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_rom');

$installer->addAttribute('catalog_product', 'ss4_talk', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtalstid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_talk');

$installer->addAttribute('catalog_product', 'ss4_singlecoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Single core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_singlecoreprocessor');

$installer->addAttribute('catalog_product', 'ss4_screensize', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Skärmstorlek',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_screensize');

$installer->addAttribute('catalog_product', 'ss4_screenresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Skärmupplösning',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_screenresolution');

$installer->addAttribute('catalog_product', 'ss4_smartphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Smartphone',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_smartphone');

$installer->addAttribute('catalog_product', 'ss4_standbytime', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Standbytid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_standbytime');

$installer->addAttribute('catalog_product', 'ss4_shockandwaterresistant', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Stöt- och vattentålig',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_shockandwaterresistant');

$installer->addAttribute('catalog_product', 'ss4_symbian', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Symbian',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_symbian');

$installer->addAttribute('catalog_product', 'ss4_flashtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av blixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_flashtype');

$installer->addAttribute('catalog_product', 'ss4_flashtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av blixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_flashtype');

$installer->addAttribute('catalog_product', 'ss4_typeofgps', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av GPS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_typeofgps');

$installer->addAttribute('catalog_product', 'ss4_typeofdisplay', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av skärm',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_typeofdisplay');

$installer->addAttribute('catalog_product', 'ss4_videoinhd', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Video i HD',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_videoinhd');

$installer->addAttribute('catalog_product', 'ss4_videoplayback', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Videouppspelning',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_videoplayback');

$installer->addAttribute('catalog_product', 'ss4_weight', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Vikt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_weight');

$installer->addAttribute('catalog_product', 'ss4_wifiwlan', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'WiFi/WLAN',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_wifiwlan');

$installer->addAttribute('catalog_product', 'ss4_windowsphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Windows Phone',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_windowsphone');

$installer->addAttribute('catalog_product', 'ss4_wlanstandard', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'WLAN standard',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_wlanstandard');

$installer->addAttribute('catalog_product', 'ss4_otherconnections', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Övriga anslutningar',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_otherconnections');


/**/
$installer->addAttribute('catalog_product', 'ss4_admission', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Inträdesavgift',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_admission');

$installer->addAttribute('catalog_product', 'ss4_monthlyfee', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Månadsavgift',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_monthlyfee');

$installer->addAttribute('catalog_product', 'ss4_callstotele2comviq', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till Tele2/Tele4G-nätet/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_callstotele2comviq');

$installer->addAttribute('catalog_product', 'ss4_calltoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till övriga mobilnät i Sverige/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_calltoothernetworksweden');

$installer->addAttribute('catalog_product', 'ss4_openingfee', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Öppningsavgift',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_openingfee');

$installer->addAttribute('catalog_product', 'ss4_smstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_smstotele2comviqst');

$installer->addAttribute('catalog_product', 'ss4_smstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_smstotele2comviqst');

$installer->addAttribute('catalog_product', 'ss4_smstoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till övriga mobilnät i Sverige/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_smstoothernetworksweden');

$installer->addAttribute('catalog_product', 'ss4_mmstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_mmstotele2comviqst');

$installer->addAttribute('catalog_product', 'ss4_mmstoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS till övriga mobilnät i Sverige/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_mmstoothernetworksweden');

$installer->addAttribute('catalog_product', 'ss4_videocall', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Videosamtal',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_videocall');

$installer->addAttribute('catalog_product', 'ss4_callabroad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till utlandet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_callabroad');

$installer->addAttribute('catalog_product', 'ss4_voicemailmin', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Telefonsvararen/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_voicemailmin');

$installer->addAttribute('catalog_product', 'ss4_directdebitoreinvoice', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Autogiro eller e-faktura',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_directdebitoreinvoice');

$installer->addAttribute('catalog_product', 'ss4_paperinvoicepc', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Pappersfaktura/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_paperinvoicepc');

$installer->addAttribute('catalog_product', 'ss4_datavolumeincluded', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Datavolym som ingår',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_datavolumeincluded');

$installer->addAttribute('catalog_product', 'ss4_speedupto', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Hastighet upp till',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_speedupto');

$installer->addAttribute('catalog_product', 'ss4_freeminutestoallworkpcs', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria minuter till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_freeminutestoallworkpcs');

$installer->addAttribute('catalog_product', 'ss4_freesmstoanynetwork', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria SMS till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_freesmstoanynetwork');

$installer->addAttribute('catalog_product', 'ss4_freemmstoallworkpieces', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria MMS till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'SS4', 'ss4_freemmstoallworkpieces');


$installer->endSetup();