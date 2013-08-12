<?php

$installer = $this;
$installer->startSetup();

$installer->run("INSERT INTO {$this->getTable('eav_entity_type')} (`entity_type_code`, `entity_model`, `entity_table`, `increment_model`, `increment_per_store`, `additional_attribute_table`) VALUES ('quote_offer', 'sales/offer', 'sales/offer', 'eav/entity_increment_numeric', 1, NULL);");
$installer->run("ALTER TABLE {$this->getTable('sales_flat_quote_item')} CHANGE COLUMN `offer_id` `offer_id` VARCHAR(50) NULL DEFAULT NULL AFTER `parent_item_id`;");
$installer->run("ALTER TABLE {$this->getTable('sales_flat_order_item')} CHANGE COLUMN `offer_id` `offer_id` VARCHAR(50) NULL DEFAULT NULL AFTER `parent_item_id`;");
$installer->endSetup();