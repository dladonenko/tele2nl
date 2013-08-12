<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/**
 * Create binding period entity table
 */

$installer->run("
    ALTER TABLE `{$this->getTable('tele2_binding')}` CHANGE monthly_price monthly_price_with_vat decimal(5,2) NULL NULL COMMENT 'Monthly Price With VAT';
    ALTER TABLE `{$this->getTable('tele2_binding')}` ADD monthly_price_without_vat decimal(5,2) NULL NULL COMMENT 'Monthly Price Without VAT';
");

$installer->endSetup();
