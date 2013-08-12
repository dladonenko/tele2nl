<?php
class Tele2_CatalogInventory_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_VIRTUAL_STOCK = 'cataloginventory/options/virtual_stock';

    /**
     * Method check is virtual stock enabled or not
     *
     * @return bool
     */
    public function isVirtualStockActive()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_VIRTUAL_STOCK);
    }

    /**
     * @return $collection out of stock products
     */
    public function getOutOfStockProducts()
    {
        $productIds = array();
        $stockItemCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
            ->addFieldToSelect('product_id')
            ->addFieldToFilter('manage_stock', 1)
            ->addFieldToFilter('is_in_stock', Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK)
            ->load();
        foreach ($stockItemCollection as $stockItem) {
            $productIds[] = $stockItem->getProductId();
        }
        $collection = Mage::getModel('catalog/product')->getCollection();
        if ($productIds) {
            $collection->addIdFilter($productIds)
                ->addAttributeToSort('attribute_set_id', 'DESC')
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        }
        return $collection;
    }

}
