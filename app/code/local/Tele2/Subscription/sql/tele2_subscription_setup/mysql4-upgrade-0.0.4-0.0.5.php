<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
        ALTER TABLE  `".$this->getTable('tele2_subscription')."` ADD COLUMN `articleid` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Subscription ID in SS4' AFTER `name`;
    ");
$installer->endSetup();