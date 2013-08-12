<?php

class Tele2_CatalogInventory_Model_Observer
{

    /**
     * save Virtual Stock
     *
     * @param $observer
     */
    public function virtualStockSave($observer)
    {
        if (Mage::helper('tele2_catalogInventory')->isVirtualStockActive()){
            $params = Mage::app()->getRequest()->getParams();
            if (isset($params['virtual_stock']) && count($params['virtual_stock'])) {
                $productId = $observer->getEvent()->getProduct()->getId();
                $virtualStockModel = Mage::getModel('tele2_cataloginventory/virtualstock');
                $virtualStock = $virtualStockModel->getVirtualStock($productId);
                $levels = $params['virtual_stock'];
                foreach ($levels as $level => $levelValue) {
                    $data = $levelValue;
                    $data['product_id'] = $productId;
                    $data['level'] = $level;
                    $data['amount'] = (isset($data['amount']) && $data['amount']) ? $data['amount'] : null;
                    $data['left'] = (isset($data['left']) && $data['left']) ? $data['left'] : $data['amount'];
                    if (isset($virtualStock[$level])) {
                        $data['virtualstock_id'] = $virtualStock[$level]->getVirtualstockId();
                    } else {
                        $virtualStock[$level] = $virtualStockModel;
                        $data['virtualstock_id'] = null;
                    }
                    $virtualStock[$level]->setData($data);
                    $virtualStock[$level]->save();
                    $virtualStock[$level]->unsetData();
                }
                // delete extra levels
                if (count($virtualStock) > count($levels)) {
                    $virtualStockLevel = $virtualStockModel->getCollection()
                        ->addFieldToFilter("product_id", array('eq' => $productId))
                        ->addFieldToFilter("level", array('gt' => count($levels)));
                    foreach ($virtualStockLevel as $virtualStockLevelDelete) {
                        $virtualStockLevelDelete->delete();
                    }
                }
            }
        }
    }

    /**
     * Activation, deactivation and decrease Virtual Stock
     *
     * @param $observer
     */
    function virtualStockUse($observer)
    {
        if (Mage::helper('tele2_catalogInventory')->isVirtualStockActive()){
            $quote = $observer->getEvent()->getQuote();
            foreach ($quote->getAllItems() as $item) {
                // for SIMPLE product with attribute name Device Or Dongle
                if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE &&
                    Mage::helper('tele2_catalog/data')->isDeviceOrDongle($item->getProduct())) {
                    $virtualStockModel = Mage::getModel('tele2_cataloginventory/virtualstock');
                    $expectedDeliveryLevel = $virtualStockModel->getExpectedDeliveryLevel($item->getProductId());
                    $stockItemModel = Mage::getModel("catalogInventory/stock_item")
                        ->load($item->getProductId(), 'product_id');
                    if ($stockItemModel->getIsInStock() == Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK
                        && $stockItemModel->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_NO) {
                        if ($expectedDeliveryLevel->getLevel() > 0 && $stockItemModel->getQty() < 0) {
                            // decrease left in level
                            $expectedDeliveryLevel->decreaseLeftInLevel($item->getQty());
                            $virtualStockLeft = $virtualStockModel->getExpectedDeliveryLevel($item->getProductId());
                            if (!$virtualStockLeft->getLevel()) {
                                // turn OFF in ("Stock Availability", "Backorders") as virtual stock
                                $stockItemModel->setUseConfigBackorders(0);
                                $stockItemModel->setBackorders(Mage_CatalogInventory_Model_Stock::BACKORDERS_NO);
                                $stockItemModel->setIsInStock(Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK);
                                $stockItemModel->save();
                            }
                        }
                    } else {
                        if ($expectedDeliveryLevel->getLevel() > 0) {
                            // turn ON in ("Stock Availability", "Backorders") as virtual stock
                            $stockItemModel->setUseConfigBackorders(0);
                            $stockItemModel->setBackorders(Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY);
                            $stockItemModel->setIsInStock(Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK);
                            $stockItemModel->save();
                        }
                    }
                }
            }
        }
    }


    public function getOutOfStockProducts()
    {
        $collection = Mage::helper('tele2_catalogInventory')->getOutOfStockProducts();
        $collection->addAttributeToSelect('name');
        return $collection;
    }

    /**
     * @todo: set template data
     * @return bool
     */
    public function checkStock()
    {
        if (!Mage::getStoreConfigFlag('cataloginventory/options/outofstock_alert_send')) {
            return false;
        }

        $outOfStockProducts = $this->getOutOfStockProducts();
        if (count($outOfStockProducts)) {
            $text = '';
            foreach ($outOfStockProducts as $product) {
                $text .= $product->getName() . '(' .  Mage::getBaseUrl() . 'admin/catalog_product/edit/id/' . $product->getId() . ')' . "\n";
            }

            $mail = Mage::getModel('core/email');

            $mail->setSubject('Out of Stock Alert');
            $mail->setBody("Out of Stock products:\n" . $text);
            $mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
            $mail->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'));

            $toAddresses = explode(',', Mage::getStoreConfig('cataloginventory/options/outofstock_alert_address'));
            $mail->setToEmail($toAddresses);
            $mail->setToName('StockAdmin');
            //$mail->setTemplate('comviq/default/template/email/outofstock.phtml');
            //$mail->setTemplateVar(array('text'=>$text));

            $mail->send();
        }
    }
}
