<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  `".$this->getTable('sales/quote')."` ADD COLUMN `assistant_data` VARCHAR(100) NULL COMMENT 'Purchase Assistant Encrypted Data' AFTER `offer_data`;");
$installer->run("ALTER TABLE  `".$this->getTable('sales/order')."` ADD COLUMN `assistant_data` VARCHAR(100) NULL COMMENT 'Purchase Assistant Encrypted Data' AFTER `offer_data`;");

$installer->endSetup();