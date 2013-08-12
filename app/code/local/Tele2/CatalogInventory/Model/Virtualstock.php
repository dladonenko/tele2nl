<?php
class Tele2_CatalogInventory_Model_Virtualstock extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param array $data
     * @param Mage_Core_Model_Config $config
     */
    public function __construct($data = array(), $config = null)
    {
        parent::__construct($data);
        $this->_config = ($config == null ? Mage::getConfig() : $config);
    }


    protected function _construct()
    {
        $this->_init('tele2_cataloginventory/virtualstock');
    }

    /**
     * return count of levels
     * @return int
     */
    public function getVirtualStockCountLevels()
    {
        return 5;
    }

    /**
     * get virtual stock levels of product
     *
     * @param mixed Mage_Catalog_Model_Product $product, (int)$product
     * @return object stock levels
     */
    public function getVirtualStock($product = null)
    {
        $levels = array();
        $productId = $this->getProductId($product);
        $levelsModel = $this->_config->getModelInstance('tele2_cataloginventory/virtualstock')->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->setOrder('level', 'asc');
        foreach ($levelsModel as $level) {
            $levels[$level->getLevel()] = $level;
        }
        return $levels;
    }

    /**
     * getExpectedDeliveryTime
     *
     * @param mixed Mage_Catalog_Model_Product $product, (int)$productId
     * @return int days (expectedDate - dateToday), out of stock = -1
     */
    public function getExpectedDeliveryTime($product = null)
    {
        $days = 0;
        foreach ($this->getVirtualStock($product) as $virtualStockLevel) {
            if ($virtualStockLevel->getLeft() > 0) {
                $date = explode("-",$virtualStockLevel->getExpectedDate());
                $checkDate = false;
                if (isset($date[1]) && isset($date[2])) {
                    $checkDate = checkdate($date[1], $date[2], $date[0]);
                }
                if ($checkDate) {
                    $datetime1 = new DateTime($virtualStockLevel->getExpectedDate());
                    $datetime2 = new DateTime(date("Y-m-d"));
                    $interval = $datetime2->diff($datetime1);
                    $days = $interval->days;
                    if ($interval->invert == 1 || $interval->days == 0) {
                        $days = 1;
                    }
                    break;
                } else {
                    $days = 1;
                }
            }
        }
        return $days;
    }

    /**
     * getExpectedDeliveryLevel
     *
     * @param $product
     * @return level $virtualStockLevel
     */
    public function getExpectedDeliveryLevel($product = null)
    {
        foreach ($this->getVirtualStock($product) as $virtualStockLevel) {
            if ($virtualStockLevel->getLeft() > 0) {
                return $virtualStockLevel;
            }
        }
        return new Varien_Object();
    }

    /**
     * @param int $qty
     * @return int $decreasedQty
     */
    public function decreaseLeftInLevel($qty = 0)
    {
        $decreasedQty = $this->getLeft() - $qty;
        if ($decreasedQty != $this->getLeft()) {
            $this->setLeft($decreasedQty);
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * getProductId from Mage_Catalog_Model_Product $product OR (int)$productId
     *
     * @param mixed $product
     * @return product id
     */
    public function getProductId($product = null)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }
        return $productId;
    }

    /**
     * daysToWeeks convert days to weeks (example 9 days = 1-2 weeks)
     * @param int days
     * @return int weeks
     */
    public function daysToWeeks($days) {
        return floor($days / 7);
    }
}
