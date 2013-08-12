<?php
    class Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Ss4response extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            $value =  $row->getData($this->getColumn()->getIndex());
            if ($value) {
                $offerData = unserialize($value);
                if (isset($offerData['ss4_order']['errorName']) && !empty($offerData['ss4_order']['errorName'])) {
                    return $offerData['ss4_order']['errorName'];
                } elseif (isset($offerData['ss4_order']['status'])) {
                    return $offerData['ss4_order']['status'];
                }
            }
            return null;
        }
    }
