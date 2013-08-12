<?php

class Tele4G_Sales_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Sales_Order
{

    protected function _prepareLayout()
    {
        $this->setChild('grid_order', $this->getLayout()->createBlock('tele4G_sales/adminhtml_sales_order_grid', 'order.grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid_order');
    }

}
