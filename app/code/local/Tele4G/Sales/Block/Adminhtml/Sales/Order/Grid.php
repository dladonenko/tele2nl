<?php

class Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->getColumn('store_id')->setIndex('main_table.store_id');

        $this->getColumn('real_order_id')
            ->setIndex('main_table.increment_id')
            ->setData('renderer', 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id');

        $this->getColumn('created_at')
            ->setIndex('main_table.created_at')
            ->setData('renderer', 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id');
        
        $this->getColumn('base_grand_total')
            ->setIndex('main_table.base_grand_total')
            ->setData('renderer', 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id');

        $this->getColumn('grand_total')
            ->setIndex('main_table.grand_total')
            ->setData('renderer', 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id');

        $this->getColumn('status')
            ->setIndex('main_table.status')
            ->setData('renderer', 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Id');

       

        $this->addColumnAfter('friend_phone',
            array(
                'header' => Mage::helper('sales')->__('Friend\'s phone'),
                'width' => '100px',
                'type'  => 'input',
                'index' => 'offer_data',
                'renderer' => 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_FriendPhone',
            ), 'shipping_name'
        );

        $this->addColumnAfter('offer_data',
            array(
                'header' => Mage::helper('sales')->__('Response from SS4'),
                'width' => '100px',
                'type'  => 'input',
                'index' => 'offer_data',
                'renderer' => 'Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_Ss4response',
            ), 'shipping_name'
        );

        $this->sortColumnsByOrder();
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()
            ->join(
                array('tele4G_order' => $collection->getTable('sales/order')),
                'main_table.entity_id = tele4G_order.entity_id',
                array(
                    'real_order_id' => 'main_table.increment_id',
                    'created_at' => 'main_table.created_at',
                    'main_table.store_id',
                    'main_table.base_grand_total',
                    'main_table.grand_total',
                    'main_table.status',
                    'offer_data',
            ));
        $this->setCollection($collection);
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}
