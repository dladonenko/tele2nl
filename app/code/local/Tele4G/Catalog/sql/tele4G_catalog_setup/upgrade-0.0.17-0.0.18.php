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


$installer->addAttributeToSet('catalog_product', 'subscription', 'General', 'usp');

$installer->endSetup();