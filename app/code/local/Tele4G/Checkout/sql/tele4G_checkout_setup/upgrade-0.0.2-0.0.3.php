<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order_item')."` ADD  `offer_id` INT( 10 ) NULL DEFAULT NULL AFTER `parent_item_id`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/quote')."` ADD COLUMN `offer_data` TEXT NULL DEFAULT NULL COMMENT 'Offer data' AFTER `reward_currency_amount`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/order')."` ADD COLUMN `offer_data` TEXT NULL DEFAULT NULL COMMENT 'Offer data' AFTER `reward_currency_amount`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/quote')."` ADD COLUMN `phone_notification` TEXT NULL DEFAULT NULL COMMENT 'Mobile phone' AFTER `reward_currency_amount`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/order')."` ADD COLUMN `phone_notification` TEXT NULL DEFAULT NULL COMMENT 'Mobile phone' AFTER `reward_currency_amount`;
");

$installer->endSetup();