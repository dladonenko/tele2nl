<?php
    class Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            return $row->getData($this->getColumn()->getId());
        }
    }
