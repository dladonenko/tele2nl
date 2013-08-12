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
 * Drop catalog product attribute band_period attributes
 */
$installer->removeAttribute('catalog_product', 'bind_period');


/**
 * Add Categories
 */
$_categories = array(
    'Subscriptions',
    'Simple Devices', //Devices
    'Configurable devices', //Devices
    'Accessories',
    'Add-ons', //Addons
);
$defaultStoreCode = "default";
$defaultStore = Mage::getModel("core/store")->load($defaultStoreCode, 'code');
$rootCategoryId = $defaultStore->getRootCategoryId();
$categoryModel = Mage::getModel('catalog/category');
$rootCategoryPath = $categoryModel->load($rootCategoryId)->getPath();
if ($rootCategoryPath && $defaultStore->getId()) {
    foreach ($_categories as $category) {
        $categoryCode = str_replace(" ","_",strtolower($category));
        $categoryModel->unsetData();
        $categoryModel
            ->setStoreId($defaultStore->getId())
            ->setPath($rootCategoryPath) //Default Category path
            ->setName($category)
            ->setMetaTitle($category)
            ->setMetaDescription($category) //$data['meta_description']; $category->addData($data);
            ->setUrlKey($category)
            ->setCode($categoryCode)
            ->setIsActive(1)
            ->setIsAnchor(0)
            ->setIncludeInMenu(1)
            ->setInfinitescroll(1)
            ->setDisplayMode('PRODUCTS_AND_PAGE')
            ->setCustomUseParentSettings(0)
            ->save();
    }
}

$installer->removeAttribute('catalog_product', 'ss4_id');
$installer->removeAttribute('catalog_product', 'ss4_shortdescription');
$installer->removeAttribute('catalog_product', 'override_name');
$installer->removeAttribute('catalog_product', 'override_subtitle');
$installer->removeAttribute('catalog_product', 'override_description');
$installer->removeAttribute('catalog_product', 'override_short_description');

$installer->addAttributeGroup('catalog_product', 'device', 'Specifications', 2);
$installer->addAttributeGroup('catalog_product', 'device', 'SS4', 3);
$installer->addAttributeGroup('catalog_product', 'subscription', 'Specifications', 2);
$installer->addAttributeGroup('catalog_product', 'subscription', 'SS4', 3);


$installer->addAttribute('catalog_product', 'ss4_name', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Item Name',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->updateAttribute('catalog_product', 'ss4_name', 'used_in_product_listing', true);
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_name', 1);

$installer->addAttribute('catalog_product', 'subtitle', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Subtitle',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'subtitle', 2);

$installer->addAttribute('catalog_product', 'ss4_subtitle', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SS4 Subtitle',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_subtitle', 2);

$installer->addAttribute('catalog_product', 'ss4_description', array(
    'user_defined'  => true,
    'type'                       => 'text',
    'label'                      => 'SS4 Description',
    'input'                      => 'textarea',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttributeToSet('catalog_product', 'device', 'SS4', 'ss4_description', 2);

$installer->addAttribute('catalog_product', 'ss4_short_description', array(
    'user_defined'  => true,
    'type'                       => 'text',
    'label'                      => 'SS4 Short Description',
    'input'                      => 'textarea',
    'required'                   => false,
    'sort_order'                 => 3,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'                      => 'SS4',
));

$installer->addAttribute('catalog_product', 'articleid', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Article Id in Super Store',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'General',
));

$installer->addAttribute('catalog_product', 'partnerid', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Partner Id',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'General',
));

$installer->addAttribute('catalog_product', 'make', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Make',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'General',
));

$installer->addAttribute('catalog_product', 'pricewithoutvat', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Price without VAT',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'Prices',
));

$installer->addAttribute('catalog_product', 'pricewithvat', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Price with VAT',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'                     => 'Prices',
));

$installer->addAttribute('catalog_product', 'autofocus', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Autofokus',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'autofocus');

$installer->addAttribute('catalog_product', 'rearcamera', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Bakåtriktad kamera',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'rearcamera');

$installer->addAttribute('catalog_product', 'batterycapacity', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Batterikapacitet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'batterycapacity');

$installer->addAttribute('catalog_product', 'bluetooth', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Blåtand',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'bluetooth');

$installer->addAttribute('catalog_product', 'bluetoothversion', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Blåtandsversion',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'bluetoothversion');

$installer->addAttribute('catalog_product', 'chipset', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Chipset',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'chipset');

$installer->addAttribute('catalog_product', 'datarate', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Datahastighet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'datarate');

$installer->addAttribute('catalog_product', 'sharpestcameraphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'De vassaste kameratelefonerna',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'sharpestcameraphone');

$installer->addAttribute('catalog_product', 'latestandhottest', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Det senaste och hetaste',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'latestandhottest');

$installer->addAttribute('catalog_product', 'dimensions', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dimensioner',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'dimensions');

$installer->addAttribute('catalog_product', 'dlna', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'DLNA',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'dlna');

$installer->addAttribute('catalog_product', 'dualcoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dual core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'dualcoreprocessor');

$installer->addAttribute('catalog_product', 'dualcoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Dual core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'dualcoreprocessor');

$installer->addAttribute('catalog_product', 'simplephonewithbuttons', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'En enkel telefon med knappar',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'simplephonewithbuttons');

$installer->addAttribute('catalog_product', 'email', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'E-post',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'email');

$installer->addAttribute('catalog_product', 'flightmode', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Flight mode',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'flightmode');

$installer->addAttribute('catalog_product', 'fmradio', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'FM-radio',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'fmradio');

$installer->addAttribute('catalog_product', 'frontcamera', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Framåtriktad kamera',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'frontcamera');

$installer->addAttribute('catalog_product', 'warranty2years', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Garantitid 2 år',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'warranty2years');

$installer->addAttribute('catalog_product', 'gps', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'GPS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'gps');

$installer->addAttribute('catalog_product', 'hdmi', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'HDMI',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'hdmi');

$installer->addAttribute('catalog_product', 'ios', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'IOS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'ios');

$installer->addAttribute('catalog_product', 'cameraover5mpix', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kamera över 5 Mpix',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'cameraover5mpix');

$installer->addAttribute('catalog_product', 'cameraflash', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kamerablixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'cameraflash');

$installer->addAttribute('catalog_product', 'rearcameraresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kameraupplösning (bak)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'rearcameraresolution');

$installer->addAttribute('catalog_product', 'frontcameraresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Kameraupplösning (fram)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'frontcameraresolution');

$installer->addAttribute('catalog_product', 'classickeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Klassisk knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'classickeypad');

$installer->addAttribute('catalog_product', 'mediatypessupported', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Mediatyper som stödjs',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'mediatypessupported');

$installer->addAttribute('catalog_product', 'memorycardslot', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Minneskortsplats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'memorycardslot');

$installer->addAttribute('catalog_product', 'memorycardtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Minneskorttyp',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'memorycardtype');

$installer->addAttribute('catalog_product', 'mms', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'mms');

$installer->addAttribute('catalog_product', 'mp3player', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MP3-spelare',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'mp3player');

$installer->addAttribute('catalog_product', 'navigationgpssoftincluded', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Navigationsmjukvara för GPS ingår',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'navigationgpssoftincluded');

$installer->addAttribute('catalog_product', 'networkbands2g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 2G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'networkbands2g');

$installer->addAttribute('catalog_product', 'networkbands3g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 3G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'networkbands3g');

$installer->addAttribute('catalog_product', 'networkbands4g', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Nätverksband 4G',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'networkbands4g');

$installer->addAttribute('catalog_product', 'operatingsystemversion', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Operativsystemversion',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'operatingsystemversion');

$installer->addAttribute('catalog_product', 'operatorlocked', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Operatörslåst',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'operatorlocked');

$installer->addAttribute('catalog_product', 'touchscreen', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Pekskärm',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'touchscreen');

$installer->addAttribute('catalog_product', 'processorspeed', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Processorhastighet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'processorspeed');

$installer->addAttribute('catalog_product', 'qwertykeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'QWERTY knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'qwertykeypad');

$installer->addAttribute('catalog_product', 'qwertykeypad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'QWERTY knappsats',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'qwertykeypad');

$installer->addAttribute('catalog_product', 'ram', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'RAM-minne (arbetsminne)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'ram');

$installer->addAttribute('catalog_product', 'rom', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'ROM-minne (lagringsminne)',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'rom');

$installer->addAttribute('catalog_product', 'talk', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtalstid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'talk');

$installer->addAttribute('catalog_product', 'singlecoreprocessor', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Single core processor',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'singlecoreprocessor');

$installer->addAttribute('catalog_product', 'screensize', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Skärmstorlek',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'screensize');

$installer->addAttribute('catalog_product', 'screenresolution', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Skärmupplösning',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'screenresolution');

$installer->addAttribute('catalog_product', 'smartphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Smartphone',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'smartphone');

$installer->addAttribute('catalog_product', 'standbytime', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Standbytid',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'standbytime');

$installer->addAttribute('catalog_product', 'shockandwaterresistant', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Stöt- och vattentålig',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'shockandwaterresistant');

$installer->addAttribute('catalog_product', 'symbian', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Symbian',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'symbian');

$installer->addAttribute('catalog_product', 'flashtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av blixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'flashtype');

$installer->addAttribute('catalog_product', 'flashtype', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av blixt',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'flashtype');

$installer->addAttribute('catalog_product', 'typeofgps', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av GPS',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'typeofgps');

$installer->addAttribute('catalog_product', 'typeofdisplay', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Typ av skärm',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'typeofdisplay');

$installer->addAttribute('catalog_product', 'videoinhd', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Video i HD',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'videoinhd');

$installer->addAttribute('catalog_product', 'videoplayback', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Videouppspelning',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'videoplayback');

$installer->addAttribute('catalog_product', 'wifiwlan', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'WiFi/WLAN',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'wifiwlan');

$installer->addAttribute('catalog_product', 'windowsphone', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Windows Phone',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'windowsphone');

$installer->addAttribute('catalog_product', 'wlanstandard', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'WLAN standard',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'wlanstandard');

$installer->addAttribute('catalog_product', 'otherconnections', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Övriga anslutningar',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'device', 'Specifications', 'otherconnections');


/**/
$installer->addAttribute('catalog_product', 'admission', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Inträdesavgift',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'admission');

$installer->addAttribute('catalog_product', 'monthlyfee', array(
    'user_defined'               => true,
    'type'                       => 'decimal',
    'label'                      => 'Månadsavgift',
    'required'                   => false,
    'input'                      => 'price',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Prices', 'monthlyfee');

$installer->addAttribute('catalog_product', 'callstotele2comviq', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till Tele2/Tele4G-nätet/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'callstotele2comviq');

$installer->addAttribute('catalog_product', 'calltoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till övriga mobilnät i Sverige/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'calltoothernetworksweden');

$installer->addAttribute('catalog_product', 'openingfee', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Öppningsavgift',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'openingfee');

$installer->addAttribute('catalog_product', 'smstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'smstotele2comviqst');

$installer->addAttribute('catalog_product', 'smstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'smstotele2comviqst');

$installer->addAttribute('catalog_product', 'smstoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'SMS till övriga mobilnät i Sverige/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'smstoothernetworksweden');

$installer->addAttribute('catalog_product', 'mmstotele2comviqst', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS till Tele2/Tele4G/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'mmstotele2comviqst');

$installer->addAttribute('catalog_product', 'mmstoothernetworksweden', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'MMS till övriga mobilnät i Sverige/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'mmstoothernetworksweden');

$installer->addAttribute('catalog_product', 'videocall', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Videosamtal',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'videocall');

$installer->addAttribute('catalog_product', 'callabroad', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Samtal till utlandet',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'callabroad');

$installer->addAttribute('catalog_product', 'voicemailmin', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Telefonsvararen/min',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'voicemailmin');

$installer->addAttribute('catalog_product', 'directdebitoreinvoice', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Autogiro eller e-faktura',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'directdebitoreinvoice');

$installer->addAttribute('catalog_product', 'paperinvoicepc', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Pappersfaktura/st',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'paperinvoicepc');

$installer->addAttribute('catalog_product', 'datavolumeincluded', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Datavolym som ingår',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'datavolumeincluded');

$installer->addAttribute('catalog_product', 'speedupto', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Hastighet upp till',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'speedupto');

$installer->addAttribute('catalog_product', 'freeminutestoallworkpcs', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria minuter till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'freeminutestoallworkpcs');

$installer->addAttribute('catalog_product', 'freesmstoanynetwork', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria SMS till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'freesmstoanynetwork');

$installer->addAttribute('catalog_product', 'freemmstoallworkpieces', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'label'                      => 'Antal fria MMS till alla operat¿rer',
    'required'                   => false,
    'input'                      => 'text',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttributeToSet('catalog_product', 'subscription', 'Specifications', 'freemmstoallworkpieces');


/**
 * old 0.0.3-0.0.4
 */
$installer->updateAttribute('catalog_product', 'variant_master', 'is_configurable', 0);

$attributeSetName = 'dongle';
$defaultSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityTypeId = $installer->getEntityTypeId('catalog_product');

if (!$installer->getAttributeSet('catalog_product', $attributeSetName)) {

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

if (!$installer->getAttribute('catalog_product', 'brand')) {
    $installer->addAttribute('catalog_product', 'brand', array(
        'user_defined'               => true,
        'type'                       => 'int',
        'source'                     => 'eav/entity_attribute_source_table',
        'label'                      => 'Brand',
        'required'                   => false,
        'input'                      => 'select',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'is_configurable'            => true,
        'option' => array (
            'value' => array(
                'brand_apple'=>array(0=>'Apple'),
                'brand_samsung'=>array(0=>'Samsung'),
                'brand_htc'=>array(0=>'HTC'),
                'brand_lg'=>array(0=>'LG'),
                'brand_sony'=>array(0=>'Sony'),
                'brand_nokia'=>array(0=>'Nokia'),
            ),
        ),
        'searchable'                 => true,
        'visible_in_advanced_search' => true,
        'visible_on_front'           => true,
        'comparable'                 => true,
        'filterable'                 => 1,
        'filterable_in_search'       => true,
        'position'                   => 1
    ));

    $installer->addAttributeToSet('catalog_product', 'device', 'General', 'brand', 5);
}


$installer->endSetup();