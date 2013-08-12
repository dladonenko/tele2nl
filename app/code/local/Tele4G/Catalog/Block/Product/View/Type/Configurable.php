<?php

class Tele4G_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * Get sorted Associated Products
     *
     * @param $product configurable
     *
     * @return array
     */
    function getSortedAssociatedProducts(Mage_Catalog_Model_Product $product)
    {
        $associatedProductsSorted = array();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $associatedProducts = $product->getTypeInstance()->getUsedProducts();
            if (is_array($associatedProducts)) {
                $virtualStockModel = Mage::getModel('tele2_cataloginventory/virtualstock');
                foreach ($associatedProducts as $associatedProduct) {
                    $aProduct = array();
                    $aProduct['product'] = $associatedProduct;
                    $aProduct['inStock'] = false;
                    $aProduct['expectedDays'] = 0;
                    $stockItemModel = Mage::getModel("catalogInventory/stock_item")->load($associatedProduct->getId(), 'product_id');
                    if ($associatedProduct->getIsInStock() == Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK) {
                        $aProduct['inStock'] = true;
                        $aProduct['expectedDays'] = 1;
                        if ($stockItemModel->getQty() <=0 && $stockItemModel->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY) {
                            $aProduct['expectedDays'] = $virtualStockModel->getExpectedDeliveryTime($associatedProduct);
                        }
                    }
                    $aProduct['expectedWeeks'] = "";
                    if ($aProduct['expectedDays'] > 7) {
                        $aProduct['expectedWeeks'] = $virtualStockModel->daysToWeeks($aProduct['expectedDays']);
                    }
                    $aProduct['is_master'] = false;
                    if ($associatedProduct->getVariantMaster()) {
                        $aProduct['is_master'] = true;
                    }
                    $associatedProductsSorted[] = $aProduct;
                }
                $expectedDays = $isMaster = $inStock = array();
                foreach ($associatedProductsSorted as $key => $value) {
                    $inStock[$key] = $value['inStock'];
                    $expectedDays[$key] = $value['expectedDays'];
                    $isMaster[$key] = $value['is_master'];
                }
                array_multisort($inStock, SORT_DESC, $expectedDays, SORT_ASC, $isMaster, SORT_DESC, $associatedProductsSorted);
            }
        }
        return $associatedProductsSorted;
    }
}
