<?php
$installer = $this;
$installer->startSetup();

$installer->run("
        ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_subtotal`;
        ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_monthly_price_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `monthly_price_amount`;
        ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `least_total_cost` DECIMAL( 10, 2 ) NULL DEFAULT '0.0000' AFTER `base_monthly_price_amount`;
    ");

$installer->endSetup();
