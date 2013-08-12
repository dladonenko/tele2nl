<?php
class Tele4G_Common_Helper_Data extends Tele2_Install_Helper_Data
{
    const SEGMENT_PIXEL_COMVIQ_POSTPAID = "http://ib.adnxs.com/seg?add=501218&t=2";
    const SEGMENT_PIXEL_COMVIQ_PREPAID = "http://ib.adnxs.com/seg?add=501219&t=2";
    
    protected $_jsArrayProduct = array();
    protected $_jsOrder;
    
    protected $_attributeSetNames;
    protected $_subscriptions;
    protected $_subscriptionsFake;

    /**
     * setJsArrayProduct
     * 
     * @param string $value
     */
    public function setJsArrayProduct($value)
    {
        if ($value) {
            $this->_jsArrayProduct[] = $value;
        }
    }
    
    /**
     * getJsArrayProduct
     * 
     * @return array
     */
    public function getJsArrayProduct()
    {
        return $this->_jsArrayProduct;
    }
    
    /**
     * 
     * @param type $order
     * @return string
     */
    public function getCity($order)
    {
        $cityName = "UNKNOWN";
        if ($order) {
            $cityName = $order->getBillingAddress()->getCity();
        }
        return $cityName;
    }
    
    /**
     * getPaymentMethod
     * 
     * @param type $order
     * @return string
     */
    public function getPaymentMethod($order)
    {
        $paymentMethod = "UNKNOWN";
        if ($order) {
            $paymentMethod = $order->getPayment()->getMethod();
        }
        return $paymentMethod;
    }
    
    /**
     * getOrderId
     * 
     * @param type $order
     * @return type
     */
    public function getOrderId($order)
    {
        return $order->getIncrementId();
    }
    
    /**
     * _setAttributeSetNameByItems
     * 
     * @param type $_items
     */
    protected function _setAttributeSetNameByItems($_items)
    {
        foreach ($_items as $item) {
            $itemsAttributeSetIds[] = $item->getProduct()->getAttributeSetId();
        }
        $itemsAttributeSetIds = array_unique($itemsAttributeSetIds);
        $this->_attributeSetNames = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', array('in' => $itemsAttributeSetIds))
            ->load()
            ->getItems();
        
        $this->_setSubscriptionsByItems($_items);
    }
    
    /**
     * _getSubscriptionsByItems
     * 
     * @param type $_items
     */
    protected function _setSubscriptionsByItems($_items)
    {
        $_subscriptions = array();
        $_subscriptionsFake = array();
        foreach ($_items as $item) {
            $attributeSetName = $this->_attributeSetNames[$item->getProduct()->getAttributeSetId()]->getAttributeSetName();
            if ($attributeSetName == self::ATTR_SET_DEVICE) {
                if (preg_match('%subscr-(\d+)-(\d+)%', $item->getSku(), $subscription)) {
                    if (isset($subscription[1])) {
                        $_subscriptions[] = $subscription[1];
                        $item->setSubscriptionIdFromSku($subscription[1]);
                    }
                }
            } elseif ($attributeSetName == self::ATTR_SET_SUBSCRIPTION) {
                $_subscriptionsFake[] = $item->getProduct()->getId();
            }
        }
        
        if (count($_subscriptions)) {
            $this->_subscriptions = Mage::getModel('tele2_subscription/mobile')
                ->getCollection()
                ->addFieldToFilter('subscription_id', array('in' => $_subscriptions))
                ->load()
                ->getItems();
        }
        
        if (count($_subscriptionsFake)) {
            $this->_subscriptionsFake = Mage::getModel('tele2_subscription/mobile')
                ->getCollection()
                ->addFieldToFilter('fake_product_id', array('in' => $_subscriptionsFake))
                ->load()
                ->getItems();
        }
    }
    
    /**
     * _getSubscriptionFake
     * 
     * @param type $productId
     * @return $subscriptionFake
     */
    protected function _getSubscriptionFake($productId)
    {
        foreach ($this->_subscriptionsFake as $subscriptionFake) {
            if ($subscriptionFake->getFakeProductId() == $productId) {
                return $subscriptionFake;
            }
        }
        return null;
    }

    /**
     * getOrderItems
     * 
     * @param type $order
     * @return string
     */
    public function getOrderItems($order) 
    {
        $_items = $order->getItemsCollection();
        $this->_setAttributeSetNameByItems($_items);
        foreach ($_items as $item) {
            $trackingCategory = "Unknown";

            if ($item->getParentItem()) continue;
            
            $attributeSetName = $this->_attributeSetNames[$item->getProduct()->getAttributeSetId()]->getAttributeSetName();

            $offerDate = unserialize($order->getOfferData());
            $activationType = (isset($offerDate[$item->getOfferId()]['type']) ? $offerDate[$item->getOfferId()]['type'] : "");

            if ($attributeSetName == self::ATTR_SET_DEVICE) {
                $_subscription = $this->_subscriptions[$item->getSubscriptionIdFromSku()];
                if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                    //activation type
                    if ($activationType == "PORT") {
                        $trackingCategory = "Tele4G Voice Portering";
                    } else {
                        $trackingCategory = "Tele4G Voice";
                    }

                } else if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST){
                    if ($activationType == "PORT") {
                        $trackingCategory = "PostPaid Tele4G Voice Portering";
                    } else {
                        $trackingCategory = "PostPaid Tele4G Voice";
                    }
                }
                $this->addProduct($trackingCategory, $item->getProduct()->getName(), $item->getProduct()->getUrlKey(), $this->getTrackingPointName($attributeSetName));

            } elseif ($attributeSetName == self::ATTR_SET_DONGLE) {
                $trackingCategory = "Tele4G Surf";
                $this->addProduct($trackingCategory, $item->getProduct()->getName(), $item->getProduct()->getUrlKey(), $this->getTrackingPointName($attributeSetName));
            } elseif ($attributeSetName == self::ATTR_SET_SUBSCRIPTION) {
                $_subscription = $this->_getSubscriptionFake($item->getProduct()->getId());
                if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE) {
                    $trackingCategory = "Tele4G Surf SIM";
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                    if ($activationType == "PORT") {
                        $trackingCategory = "Tele4G Voice Portering";
                    } else {
                        $trackingCategory = "Tele4G Voice";
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                    if ($activationType == "PORT") {
                        $trackingCategory = "PostPaid Tele4G Voice Portering";
                    } else {
                        $trackingCategory = "PostPaid Tele4G Voice";
                    }
                }
                $this->addProduct($trackingCategory, $item->getProduct()->getName(), $item->getProduct()->getUrlKey(), $this->getTrackingPointName($attributeSetName));
            }
        }
        
        return implode("\n", $this->getJsArrayProduct());
    }

    /**
     * getTrackingPointName
     * 
     * @param type $type_product
     * @return string
     */
    public function getTrackingPointName($type_product = null)
    {
        $trackingPointName = array(
            self::ATTR_SET_DEVICE => "mobiltelefoner",
            self::ATTR_SET_DONGLE => "mobilt-bredband", 
            self::ATTR_SET_ACCESSORY => "tillbehor",
            self::ATTR_GROUP_ADDON => "pluspaket", 
            self::ATTR_SET_SUBSCRIPTION => "abonnemang", 
            "undefined" => "ovrigt"
        );
        
        if (isset($trackingPointName[$type_product])) {
            return $trackingPointName[$type_product];
        } else {
            return $trackingPointName['undefined'];
        }
    }

    /**
     * createOrder
     * 
     * @param type $ageGroup
     * @param type $genderCode
     * @param type $city
     * @param type $paymentMethod
     * @param type $orderId
     * @return type
     */
    public function createOrder($ageGroup, $genderCode, $city, $paymentMethod, $orderId)
    {
        $this->_jsOrder = "adf.createOrder({sv4:'{$ageGroup}', sv5:'{$genderCode}', sv6:'{$city}', sv7:'{$paymentMethod}', orderid:'{$orderId}'});\n";
        return $this->_jsOrder;
    }

    /**
     * addProduct
     * 
     * @param type $type
     * @param type $deviceName
     * @param type $bundleName
     * @param type $type_product
     */
    public function addProduct($type, $deviceName, $bundleName, $type_product) 
    {
        $jsProduct = "adf.addProduct({sv1:'{$type}', sv2:'{$deviceName}', sv3:'{$bundleName}', sv12:'{$type_product}'});";
        $this->setJsArrayProduct($jsProduct);
    }
    
    protected function _getOrderMock()
    {
        $ii = 0;
        $orderIds = range(1, 1999);
        shuffle($orderIds);
        $order = Mage::getModel('sales/order');
        while ($order->load($orderId=array_shift($orderIds))) {
        //while ($order->load(20346)) {
            $items = $order->getItemsCollection();
            if ($items->count()) {
                break;
            }
            if (++$ii == 999) break;
        }
        return $order;
    }

    /**
     * getTracking
     * 
     * @param type $order
     * @return Varien_Object
     */
    public function getTracking($order = null)
    {
        if (!$order) {
            $order = $this->_getOrderMock();
        }
        $tracking = new Varien_Object();
        
        $ssn = $order->getSsn();
        $city = $this->getCity($order);
        $adFormHelper  = Mage::helper('tele4G_adform');
        $genderCode = $adFormHelper->getGenderFromSsn($ssn);
        $ageGroup = $adFormHelper->getAgeGroup($ssn);
        $orderId = $this->getOrderId($order);
        $paymentMethod = $this->getPaymentMethod($order);

        $tracking->setCreateOrder($this->createOrder($ageGroup, $genderCode, $city, $paymentMethod, $orderId));
        $tracking->setAddProducts($this->getOrderItems($order));
        return $tracking;
    }
    
}
