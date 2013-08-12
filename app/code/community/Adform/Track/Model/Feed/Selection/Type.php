<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Model_Feed_Selection_Type
{
    public function toOptionArray()
    {
        return array(
            'all' => Mage::helper('adminhtml')->__('All Products'),
            'selected' => Mage::helper('adminhtml')->__('Selected Products')
        );
    }
}