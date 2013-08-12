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

$attributeSetName = 'insurance';
if (!$installer->getAttributeSet('catalog_product', $attributeSetName)) {
    
    // create AttributeSet
    $defaultSetId = $installer->getAttributeSetId('catalog_product', 'Default');
    $entityTypeId = $installer->getEntityTypeId('catalog_product');
    $attributeSet = Mage::getModel('eav/entity_attribute_set');
    $attributeSet
        ->setEntityTypeId($entityTypeId)
        ->setAttributeSetName($attributeSetName);
    if ($attributeSet->validate()) {
        $attributeSet->save();
        $attributeSet->initFromSkeleton($defaultSetId);
        $attributeSet->save();
    }
    
    // create Attribute and attache their to AttributeSet 
    $installer->addAttribute('catalog_product', 'logistics_article_id', array(
        'user_defined'               => true,
        'type'                       => 'varchar',
        'source'                     => 'eav/entity_attribute_source_boolean',
        'label'                      => 'Logistics Article Id',
        'required'                   => false,
        'input'                      => 'text',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'default'                    => 0,
        'visible_on_front'           => false,
    ));
    $installer->addAttributeToSet('catalog_product', $attributeSetName, 'General', 'logistics_article_id', 2);
    
    $installer->addAttribute('catalog_product', 'product_code', array(
        'user_defined'               => true,
        'type'                       => 'varchar',
        'source'                     => 'eav/entity_attribute_source_boolean',
        'label'                      => 'Product Code',
        'required'                   => false,
        'input'                      => 'text',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'default'                    => 0,
        'visible_on_front'           => false,
    ));
    $installer->addAttributeToSet('catalog_product', $attributeSetName, 'General', 'product_code', 3);
    
    $installer->addAttribute('catalog_product', 'insured_months', array(
        'user_defined'               => true,
        'type'                       => 'varchar',
        'source'                     => 'eav/entity_attribute_source_boolean',
        'label'                      => 'Insured Months',
        'required'                   => false,
        'input'                      => 'text',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'default'                    => 0,
        'visible_on_front'           => true,
    ));
    $installer->addAttributeToSet('catalog_product', $attributeSetName, 'General', 'insured_months', 3);

    // Add Category
    $defaultStoreCode = "default";
    $defaultStore = Mage::getModel("core/store")->load($defaultStoreCode, 'code');
    $rootCategoryId = $defaultStore->getRootCategoryId();
    $categoryModel = Mage::getModel('catalog/category');
    $rootCategoryPath = $categoryModel->load($rootCategoryId)->getPath();
    if ($rootCategoryPath && $defaultStore->getId()) {
        $category = 'Insurances';
        $categoryModel->unsetData();
        $categoryModel
            ->setStoreId($defaultStore->getId())
            ->setPath($rootCategoryPath) //Default Category path
            ->setName($category)
            ->setMetaTitle($category)
            ->setMetaDescription($category) //$data['meta_description']; $category->addData($data);
            ->setUrlKey($category)
            ->setCode($category)
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