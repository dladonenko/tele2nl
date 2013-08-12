<?php

class Tele2_CatalogInventory_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
   public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/tab/inventory.phtml');
    }

    /**
     * HTML interface of virtual stock
     *
     * @return html
     */
    public function getVirtualStockHtml()
    {
        return Mage::helper('tele2_catalogInventory')->isVirtualStockActive() ?
            $this->setTemplate('catalog/product/tab/virtualStock.phtml')->toHtml() : '';
    }

}
