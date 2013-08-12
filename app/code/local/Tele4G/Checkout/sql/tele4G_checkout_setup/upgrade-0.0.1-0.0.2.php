<?php

$installer = $this;
$installer->startSetup();

$installer->run("
        ALTER TABLE  `".$this->getTable('sales/quote_item')."` ADD  `offer_id` INT( 10 ) NULL DEFAULT NULL AFTER `parent_item_id`;
    ");

$installer->endSetup();