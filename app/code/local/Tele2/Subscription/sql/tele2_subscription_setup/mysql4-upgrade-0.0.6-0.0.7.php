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

$table = $installer->getConnection()->addColumn(
    $installer->getTable('tele2_subscription'),
    'subsidy_price',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'default'   => null,
        'nullable'  => true,
        'scale'     => 2,
        'precision' => 5,
        'comment'   => 'Subsidy Price'
    )
);

$installer->run("
    ALTER TABLE `{$this->getTable('tele2_binding')}` CHANGE invoice_price monthly_price decimal(5,2) NULL NULL COMMENT 'Monthly Price';
    ALTER TABLE `{$this->getTable('tele2_binding')}` CHANGE billing_code article_id varchar(100) NULL NULL COMMENT 'Article Id';
");

$installer->endSetup();