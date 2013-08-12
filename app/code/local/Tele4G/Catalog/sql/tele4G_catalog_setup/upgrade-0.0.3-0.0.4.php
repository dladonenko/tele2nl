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

// Update attribute 'code'
$installer->updateAttribute('catalog_category', 'code', 'is_required', 'false');


// Create categories ("Mobile broadband", "Brands")
$_categories = array(
    'Mobile broadband',
    'Brands',
);
$defaultStoreCode = "default";
$defaultStore = Mage::getModel("core/store")->load($defaultStoreCode, 'code');
$rootCategoryId = $defaultStore->getRootCategoryId();
$categoryModel = Mage::getModel('catalog/category');
$rootCategoryPath = $categoryModel->load($rootCategoryId)->getPath();
if ($rootCategoryPath && $defaultStore->getId()) {
    foreach ($_categories as $category) {
        $categoryCode = str_replace(" ","_",strtolower($category));
        $categoryModel->unsetData();
        $categoryModel
            ->setStoreId($defaultStore->getId())
            ->setPath($rootCategoryPath) //Default Category path
            ->setName($category)
            ->setMetaTitle($category)
            ->setMetaDescription($category) //$data['meta_description']; $category->addData($data);
            ->setUrlKey($category)
            ->setCode($categoryCode)
            ->setIsActive(1)
            ->setIsAnchor(0)
            ->setIncludeInMenu(1)
            ->setInfinitescroll(1)
            ->setDisplayMode('PRODUCTS_AND_PAGE')
            ->setCustomUseParentSettings(0)
            ->save();
    }
}

$installer->endSetup();
