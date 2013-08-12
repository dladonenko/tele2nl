<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vyko
 * Date: 9/17/12
 * Time: 10:33 AM
 * To change this template use File | Settings | File Templates.
 */

class Tele4G_Catalog_Model_Observer
{
    public function addAttributeSetHandle(Varien_Event_Observer $observer)
    {
        $product = Mage::registry('current_product');
        if(!$product){
            return;
        }
        
        $attributeGroup = $product->getAttributeText('subscription_group');
        if($attributeGroup != 'sim Only'){
            return;
        }
        
        /**
         * Return if it is not product page
         */
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return;
        }
 
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId());
        /**
         * Convert attribute set name to alphanumeric + underscore string
         */
        $niceName = str_replace('-', '_', $product->formatUrlKey($attributeSet->getAttributeSetName()));
 
        /* @var $update Mage_Core_Model_Layout_Update */
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $update->addHandle('PRODUCT_ATTRIBUTE_SET_' . $niceName);
       
    }
}
