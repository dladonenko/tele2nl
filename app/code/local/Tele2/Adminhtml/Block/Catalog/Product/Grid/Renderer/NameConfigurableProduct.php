<?php
    class Tele2_Adminhtml_Block_Catalog_Product_Grid_Renderer_NameConfigurableProduct extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            $value =  $row->getData($this->getColumn()->getIndex());
            if ($value > 0) {
                $ParentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($value);
                if (isset($ParentIds[0])) {
                    return Mage::getModel('catalog/product')->load($ParentIds[0])->getName();
                }
            }
            return null;
        }
    }
