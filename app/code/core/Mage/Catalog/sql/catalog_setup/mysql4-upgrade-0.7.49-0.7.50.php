<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$this->startSetup();

// get options_container attribute and update its value to 'container1' for configurable products
$attribute = $this->getAttribute('catalog_product', 'options_container');
if (!empty($attribute['attribute_id'])) {
    $this->run("
        UPDATE {$this->getTable('catalog_product_entity_varchar')}
        SET value = 'container1'
        WHERE
            entity_id IN (SELECT entity_id FROM {$this->getTable('catalog_product_entity')} WHERE type_id='configurable')
            AND attribute_id={$attribute['attribute_id']}
    ");
}

$this->endSetup();
