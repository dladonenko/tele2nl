<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/quote')."` ADD COLUMN `additional` TEXT NULL DEFAULT NULL COMMENT 'Facktura' AFTER `reward_currency_amount`;
");



$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD COLUMN `additional` TEXT NULL DEFAULT NULL COMMENT 'Facktura' AFTER `reward_currency_amount`;
");


$installer->endSetup();