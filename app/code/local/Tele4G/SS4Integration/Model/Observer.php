<?php
class Tele4G_SS4Integration_Model_Observer extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('tele4G_sS4Integration/observer');
    }

    /**
     * Updates popular devices
     * @todo: add filters by views or ordered qty
     * @return Tele4G_SS4Integration_Model_Observer
     */
    public function updatePopularDevices()
    {
        //$updateStartTime = microtime(true);

        //$devices = $this->getMostviewed();
        $devices = $this->getProducts();
        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load('device', 'attribute_set_name')
            ->getAttributeSetId();
        $devices->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
            ->setOrder('qty', 'asc')
            ->setPageSize(25)->setCurPage(1);

        if ($devices->getSize()) {
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_MANUAL);
            foreach ($devices as $device) {
                if ($device->getId() && $device->getPartnerid()) {
                    $this->updateProduct($device);
                }
            }
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_REAL_TIME);
        }

        /*$updateEndTime = microtime(true);
        Mage::log('TOTAL UPDATE TIME ' . round(($updateEndTime - $updateStartTime), 3) . ' sec.', null, 'updatePopularDevices.log');*/
        return $this;
    }

    /**
     * Updates all devices except popular
     * @todo: exclude popularity
     * @return Tele4G_SS4Integration_Model_Observer
     */
    public function updateAllDevices()
    {
        //$updateStartTime = microtime(true);

        $devices = $this->getProducts();
        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load('device', 'attribute_set_name')
            ->getAttributeSetId();
        $devices->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
            ->setOrder('qty', 'asc')
            ->setPageSize(50)->setCurPage(1);

        if ($devices->getSize()) {
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_MANUAL);
            foreach ($devices as $device) {
                if ($device->getId() && $device->getPartnerid()) {
                    $this->updateProduct($device);
                }
            }
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_REAL_TIME);
        }

        /*$updateEndTime = microtime(true);
        Mage::log('TOTAL UPDATE TIME ' . round(($updateEndTime - $updateStartTime), 3) . ' sec.', null, 'updateAllDevices.log');*/
        return $this;
    }

    /**
     * Updates all accessories
     * @todo:
     * @return Tele4G_SS4Integration_Model_Observer
     */
    public function updateAccessories()
    {
        //$updateStartTime = microtime(true);

        $accessories = $this->getProducts();
        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load('accessory', 'attribute_set_name')
            ->getAttributeSetId();
        $accessories->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
            ->setOrder('qty', 'asc')
            ->setPageSize(150)->setCurPage(1)
        ;

        if ($accessories->getSize()) {
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_MANUAL);
            
            foreach ($accessories as $device) {
                if ($device->getId() && $device->getPartnerid()) {
                    $this->updateProduct($device);
                }
            }          
            
            $this->switchIndexerMode(Mage_Index_Model_Process::MODE_REAL_TIME);
        }

        /*$updateEndTime = microtime(true);
        Mage::log('TOTAL UPDATE TIME ' . round(($updateEndTime - $updateStartTime), 3) . ' sec.', null, 'updateAccessories.log');*/
        return $this;
    }

    public function updateProduct($productItem)
    { 
        $ss4Stock = $this->getProductStock($productItem->getPartnerid());
        //$startTime = microtime(true);
        
        $stockQty = $productItem->getQty();
        if (isset($ss4Stock) && $ss4Stock <> $stockQty && !is_null($stockQty)) {
            try{
                $stockItem = Mage::getModel('cataloginventory/stock_item');
                $stockItem->setProcessIndexEvents(false);
                $stockItem->setData(array());
                $stockItem->loadByProduct($productItem->getId())->setProductId($productItem->getId());
                $stockItem->setDataUsingMethod('qty', $ss4Stock);
                if ($stockItem->dataHasChangedFor('qty')) {
                    if ($ss4Stock > 0) {
                        $stockItem->setIsInStock(Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK);
                    }
                    $stockItem->save();
                }
                
            } catch (Mage_Exception $e) {
                Mage::logException($e);
            }

            /*$endTime = microtime(true);
            $diff = round(($endTime - $startTime), 3);
            Mage::log('ITEM UPDATE TIME ' . $diff . 'sec' . ' ID ' . $productItem->getId(), null, 'updateProduct.log');*/
        }
    }

    /**
     * @param $articleId SS4 article Id
     */
    public function getProductStock($articleId)
    {
        //$startTime = microtime(true);
        $stockLevel = Mage::helper('tele4G_sS4Integration')->getStockLevel($articleId);
        if (isset($stockLevel->stockLevels->stockLevel->itemsInStock)) {
            return $stockLevel->stockLevels->stockLevel->itemsInStock;
        } else {
            return null;
        }
        
        
        
        //Mage::log('SERVICE TIME ' . round((microtime(true) - $startTime), 3) . ' sec.', null, 'service.log');
    }

    /**
     * Returns collection of products need to be updated
     * @return mixed
     */
    public function getProducts()
    {
        $products = Mage::getModel('catalog/product')->getCollection()
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            )
            ->addAttributeToSelect(array('articleid'))
            ->addAttributeToSelect(array('partnerid'))
            ->addAttributeToFilter('status', 1) //1 - enabled, 2 - disabled
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
         ;

        return $products;
    }

    /**
     * Returns bestseller collection
     * @return mixed
     */
    public function getBestsellers()
    {
        $storeId  = $this->getStoreId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect(array('articleid'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('ordered_qty', 'desc')
            ->setPageSize(5)->setCurPage(1)
        ;
        return $products;
    }

    /**
     * Returns most viewed collection
     * @return mixed
     */
    public function getMostviewed()
    {
        $storeId  = $this->getStoreId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addViewsCount()
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect(array('articleid'))
            ->addAttributeToSelect(array('partnerid'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('views_count', 'desc')
            ->setPageSize(5)->setCurPage(1)
        ;
        return $products;
    }

    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * Changes indexer mode
     *
     * @param string $mode Mage_Index_Model_Process::MODE_REAL_TIME Mage_Index_Model_Process::MODE_MANUAL
     */
    public function switchIndexerMode($mode = Mage_Index_Model_Process::MODE_REAL_TIME)
    {
        $pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($pCollection as $process) {
            $currentMode = $process->getMode();
            if ($currentMode != $mode) {
                $process->setMode($mode)->save();
            }
        }
    }

}