<?php

class Adform_Track_Model_Config_Product_Page_Tracking_Level
{
    const CATEGORY = 'category';
    const PRODUCT = 'product';
    
    public function toOptionArray()
    {
        return array(
            array('value' => self::CATEGORY, 'label'=>Mage::helper('adform_track')->__('Category')),
            array('value' => self::PRODUCT, 'label'=>Mage::helper('adform_track')->__('Product')),
        );
    }    
}
