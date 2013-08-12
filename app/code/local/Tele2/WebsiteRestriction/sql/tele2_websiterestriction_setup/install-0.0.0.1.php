<?php

/** @var $installer Tele2_WebsiteRestriction_Model_Resource_Setup */
$installer = $this;


$rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
/**
 * create Website
 */
    /** @var $website Mage_Core_Model_Website */
    $website = Mage::getModel('core/website');
    $website->setCode('mecenat')
        ->setName('Mecenat Students')
        ->save();

/**
 * create Store Group
 */
    /** @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = Mage::getModel('core/store_group');
    $storeGroup->setWebsiteId($website->getId())
        ->setName('Mecenat Students')
        ->setRootCategoryId($rootCategoryId)
        ->save();

/**
 * create Store
 */
    /** @var $store Mage_Core_Model_Store */
    $store = Mage::getModel('core/store');
    $store->setCode('mecenat')
        ->setWebsiteId($storeGroup->getWebsiteId())
        ->setGroupId($storeGroup->getId())
        ->setName('Mecenat Students')
        ->setIsActive(1)
        ->save();

