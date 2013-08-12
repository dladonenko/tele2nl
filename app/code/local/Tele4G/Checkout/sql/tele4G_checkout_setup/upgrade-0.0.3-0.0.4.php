<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/quote_item')."` ADD COLUMN `article_id` INT( 10 ) NULL DEFAULT NULL COMMENT 'Article id' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/quote_item')."` ADD COLUMN `partner_id` TEXT NULL DEFAULT NULL COMMENT 'Partner id' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/quote_item')."` ADD COLUMN `make` TEXT NULL DEFAULT NULL COMMENT 'Make' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/quote_item')."` ADD COLUMN `expected_delivery_time` TEXT NULL DEFAULT NULL COMMENT 'Expected delivery time' AFTER `sku`;
");



$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order_item')."` ADD COLUMN `article_id` INT( 10 ) NULL DEFAULT NULL COMMENT 'Article id' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/order_item')."` ADD COLUMN `partner_id` TEXT NULL DEFAULT NULL COMMENT 'Partner id' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/order_item')."` ADD COLUMN `make` TEXT NULL DEFAULT NULL COMMENT 'Make' AFTER `sku`;
");

$installer->run("
    ALTER TABLE `".$this->getTable('sales/order_item')."` ADD COLUMN `expected_delivery_time` TEXT NULL DEFAULT NULL COMMENT 'Expected delivery time' AFTER `sku`;
");

$installer->endSetup();