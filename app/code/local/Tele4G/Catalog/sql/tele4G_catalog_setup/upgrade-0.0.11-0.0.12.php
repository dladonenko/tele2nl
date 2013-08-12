<?php
/**
 *
 *
 * @category    Tele4G
 * @package     Tele4G_Catalog
 */

 /* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'sim_type', array(
    'user_defined'               => true,
    'type'                       => 'varchar',
    'source'                     => 'eav/entity_attribute_source_table',
    'label'                      => 'SIM-type',
    'required'                   => false,
    'input'                      => 'select',
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'           => false,
    'apply_to'                   => 'configurable',
    'is_configurable'            => false,
    'option' => array (
        'value' => array(
            'NONE'=>array(0=>'NONE'),
            'PRE_MOUNTED'=>array(0=>'PRE_MOUNTED'),
            'MINI_REGULAR'=>array(0=>'MINI_REGULAR'),
            'MICRO_REGULAR'=>array(0=>'MICRO_REGULAR'),
            'MINI_USIM'=>array(0=>'MINI_USIM'),
            'MICRO_USIM'=>array(0=>'MICRO_USIM'),
            'NANO_REGULAR'=>array(0=>'NANO_REGULAR'),
            'COMBO'=>array(0=>'COMBO'),
        ),
        'order' => array(
            'NONE'=>1,
            'PRE_MOUNTED'=>2,
            'MINI_REGULAR'=>3,
            'MICRO_REGULAR'=>4,
            'MINI_USIM'=>5,
            'MICRO_USIM'=>6,
            'NANO_REGULAR'=>7,
            'COMBO'=>8,
        ),
    ),
));
$installer->addAttributeToSet('catalog_product', 'device', 'General', 'sim_type');

$installer->endSetup();