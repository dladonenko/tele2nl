<?php

/** @var $installer Tele2_WebsiteRestriction_Model_Resource_Setup */
$installer = $this;


$rootCategoryId = Mage::app()->getStore()->getRootCategoryId();

  $websiteId = 1; // "Create website" programatically $website->getId(); or $storeGroup->getWebsiteId();
  $storeGroupId = 1; // "Create Store Group" programatically $storeGroup->getId();
  $storeViewName = "Dutch";
  $storeViewCode = "nl";

  // Create store object
  $store = Mage::getModel('core/store');
  try {
    $store->setCode($storeViewCode)
          ->setWebsiteId($websiteId)
          ->setGroupId($storeGroupId)
          ->setName($storeViewName)
          ->setIsActive(1)
          ->save();
  }
  catch (Exception $e){
    echo $e->getMessage();
  }
