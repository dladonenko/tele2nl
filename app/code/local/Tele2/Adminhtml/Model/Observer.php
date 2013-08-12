<?php

class Tele2_Adminhtml_Model_Observer
{
    public function addTabToDashboard(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Dashboard_Grids) {
            $block->addTab('outofstock_products', array(
                'label'     => $block->__('Out of stock'),
                'content'   => $block->getLayout()->createBlock('tele2_adminhtml/dashboard_tab_products_outofstock')->toHtml(),
                'active'    => false
            ));
        }
    }
}