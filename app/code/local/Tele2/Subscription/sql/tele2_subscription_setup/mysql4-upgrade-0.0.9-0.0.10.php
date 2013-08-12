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
 * Added subscription_group column
 */

$installer->run("ALTER TABLE `{$this->getTable('tele2_subscription')}` ADD COLUMN `subscription_group` INT NOT NULL DEFAULT '1' COMMENT 'Subscription Group' AFTER `priceplan`;");
$installer->run("CREATE TABLE `{$this->getTable('tele2_subscription_group')}` (`group_id` INT(10) NOT NULL AUTO_INCREMENT, `name` VARCHAR(250) NOT NULL, PRIMARY KEY (`group_id`)) COLLATE='utf8_general_ci' ENGINE=InnoDB;");
$installer->run("INSERT INTO `{$this->getTable('tele2_subscription_group')}` (`group_id`, `name`) VALUES (1, 'Default');");
$installer->endSetup();
