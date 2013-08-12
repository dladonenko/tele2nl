<?php
/**
 * Install script for Tele4G Checkout module
 * 
 *
 * @category    Tele4G
 * @package     Tele4G_Checkout
 */
/* @var $installer Mage_Checkout_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$installer->run("
        ALTER TABLE  `".$this->getTable('sales/quote')."` ADD  `monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_subtotal`;
        ALTER TABLE  `".$this->getTable('sales/quote')."` ADD  `base_monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `monthly_price_amount`;
        ALTER TABLE  `".$this->getTable('sales/quote')."` ADD  `least_total_cost` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_monthly_price_amount`;
            
        ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_subtotal_with_discount`;
		ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `monthly_price_amount`;
        ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `least_total_cost` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_monthly_price_amount`;

    ");

$installer->endSetup();