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

$installer->addAttributeToSet('catalog_product', 'dongle', 'General', 'usp');
$installer->addAttributeToSet('catalog_product', 'dongle', 'SS4', 'ss4_usp');

$installer->updateAttribute('catalog_product', 'frequency_2g', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'frequency_3g', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'frequency_4g', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'hard_drive_space', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'modem', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'download_speed', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'operating_systems', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'port', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'ram', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'screen_resolutions', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'upload_speed', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'outgoing_calls', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'outgoing_sms', 'is_visible_on_front', true);
$installer->updateAttribute('catalog_product', 'surf_abroad_roaming', 'is_visible_on_front', true);

$installer->endSetup();