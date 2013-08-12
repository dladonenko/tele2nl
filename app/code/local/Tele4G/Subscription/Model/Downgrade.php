<?php

class Tele4G_Subscription_Model_Downgrade extends Mage_Core_Model_Abstract
{
    
    protected $_deviceSubscriptionCollection;
    protected $_subscriptionCollection;
    
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }

    public function getSubscriptionCodeA()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        if ($filter && count($lastSubscriptionGroup['device_type']) && count($lastSubscriptionGroup['groups'])) {
            if (in_array("device", $lastSubscriptionGroup['device_type']) || in_array("subscription", $lastSubscriptionGroup['device_type'])) {
                $type1 = "ms.type1 IN (1,2)";
            } elseif (in_array("dongle", $lastSubscriptionGroup['device_type'])) {
                $type1 = "ms.type1 IN (3,4)";
            } else {
                $type1 = "ms.type1 IN (1,2)";
            }

            $pdo = Mage::getModel('core/resource')->getConnection('read')
                    ->fetchCol("
                        SELECT `s`.`fake_product_id` FROM `tele2_abstract_subscription` AS `s` JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = s.subscription_id
                        WHERE {$type1} and s.standalone = 1 and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_A."
                        AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).") "
            );

            $this->_subscriptionCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $pdo))
                ->addStoreFilter(Mage::app()->getStore(true)->getId())
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

            if (count($this->_subscriptionCollection)) {
                return $this->_subscriptionCollection;
            }
        }
        return false;
    }
    
    public function getSubscriptionCodeGN0()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        if ($filter && count($lastSubscriptionGroup['type']) && count($lastSubscriptionGroup['groups'])) {

            $pdo = Mage::getModel('core/resource')->getConnection('read')
                    ->fetchCol("
                        SELECT `s`.`fake_product_id` FROM `tele2_abstract_subscription` AS `s` JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = s.subscription_id
                        WHERE ms.type1 IN (".implode(',', $lastSubscriptionGroup['type']).") and s.standalone = 1 and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_GN0."
                        AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).") "
            );

            $this->_subscriptionCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $pdo))
                ->addStoreFilter(Mage::app()->getStore(true)->getId())
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);    

            if (count($this->_subscriptionCollection)) {
                return $this->_subscriptionCollection;
            }
        }
        return false;
    }
    
    public function getSubscriptionCodeGN1Post()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        if ($filter) {

            $pdo = Mage::getModel('core/resource')->getConnection('read')
                    ->fetchCol("
                        SELECT `s`.`fake_product_id` FROM `tele2_abstract_subscription` AS `s` JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = s.subscription_id
                        WHERE ms.type1 IN (".implode(',', $lastSubscriptionGroup['type']).") and s.standalone = '1' and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_GN1."
                        AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).") "
            );

            $this->_subscriptionCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $pdo))
                ->addStoreFilter(Mage::app()->getStore(true)->getId())
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);    

            if (count($this->_subscriptionCollection)) {
                return $this->_subscriptionCollection;
            }
        }
        return false;
    }
    
    public function getSubscriptionCodeGN1Pre()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        if ($filter) {

            $pdo = Mage::getModel('core/resource')->getConnection('read')
                    ->fetchCol("
                        SELECT `s`.`fake_product_id` FROM `tele2_abstract_subscription` AS `s` JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = s.subscription_id
                        WHERE ms.type1 IN (".implode(',', $lastSubscriptionGroup['type']).") and s.standalone = '1' and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_GN1."
                        AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).") "
            );

            $this->_subscriptionCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $pdo))
                ->addStoreFilter(Mage::app()->getStore(true)->getId())
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);    

            if (count($this->_subscriptionCollection)) {
                return $this->_subscriptionCollection;
            }
        }
        return false;
    }
    
    public function getDeviceSubscriptionCodeGN1Post()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        $aProductIds = array();
        $pdo = Mage::getModel('core/resource')->getConnection('read')
                ->fetchCol("
                    SELECT distinct `r`.`product_id` FROM `tele2_relation` AS `r`
                    JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = r.subscription_id AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).")
                    INNER JOIN `tele2_abstract_subscription` AS `s` ON s.subscription_id=r.subscription_id
                    WHERE ms.type1 IN (".implode(',', $lastSubscriptionGroup['type']).") and s.standalone = 1 and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_GN1." "
            );
        if ($pdo) {
            $collection = Mage::getModel('tele2_subscription/relation')->getCollection()
                ->addFieldToFilter('product_id', array('in' => $pdo));
        
            foreach ($collection as $collect) {
                if ($collect->getProductId()) {
                    $aProductIds[$collect->getProductId()] = $collect->getSubscriptionId();
                }
            }
            return $aProductIds;
        }
        return false;
    }
    
    public function getDeviceSubscriptionCodeGN1Pre()
    {
        $lastSubscriptionGroup = $this->getLastSubscriptionGroup();
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        $aProductIds = array();
        $pdo = Mage::getModel('core/resource')->getConnection('read')
                ->fetchCol("
                    SELECT distinct `r`.`product_id` FROM `tele2_relation` AS `r`
                    JOIN `tele2_mobile_subscription` AS `ms` ON ms.subscription_id = r.subscription_id AND ms.subscription_group IN (".implode(',', $lastSubscriptionGroup['groups']).")
                    INNER JOIN `tele2_subscription` AS `s` ON s.subscription_id=r.subscription_id
                    WHERE ms.type1 IN (".implode(',', $lastSubscriptionGroup['type']).") and s.standalone = 1 and ms.downgrade = ".Tele2_Subscription_Model_Mobile::SUBSCRIPTION_DOWNGRADE_GN1." "
            );
        if ($pdo) {
            $collection = Mage::getModel('tele2_subscription/relation')->getCollection()
                ->addFieldToFilter('product_id', array('in' => $pdo));
        
            foreach ($collection as $collect) {
                if ($collect->getProductId()) {
                    $aProductIds[$collect->getProductId()] = $collect->getSubscriptionId();
                }
            }
            return $aProductIds;
        }
        return false;
    }
    
    public function getDowngradeSubscriptionSimOnly()
    {
        $filter = $this->_getCheckout()->getDeviceSubscriptionFilter();
        $aCollection = array();
        $message = "";
        if ($filter) {
            if ($filter['error_code'] == "CREDIT_CONTROL_REJECTED") {
                $this->_downgradeSubscription = $this->getSubscriptionCodeA();
                if ($this->_downgradeSubscription) {
                    $message = "Du har fått begränsat godkännande i vår kreditupplysning. Givetvis vill vi gärna att du blir kund hos oss. Vi kan istället erbjuda dig följande:";
                    $aCollection['error_message'] = $message;
                    $aCollection['subscription'] = $this->_downgradeSubscription;
                    return $aCollection;
                }
            } elseif ($filter['error_code'] == "CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_0_NOT_SUFFICIENT") {
                $this->_downgradeSubscription = $this->getSubscriptionCodeGN0();
                if ($this->_downgradeSubscription) {
                    $message = "Du har fått begränsat godkännande i vår kreditupplysning. Givetvis vill vi gärna att du blir kund hos oss. Vi kan istället erbjuda dig följande:";
                    $aCollection['error_message'] = $message;
                    $aCollection['subscription'] = $this->_downgradeSubscription;
                    return $aCollection;
                }
                $this->_downgradeSubscription = $this->getSubscriptionCodeA();
                if ($this->_downgradeSubscription) {
                    return $this->_downgradeSubscription;
                }
            } elseif ($filter['error_code'] == "CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_1_NOT_SUFFICIENT") {
                    $this->_downgradeSubscription = $this->getSubscriptionCodeGN1Post();
                    if ($this->_downgradeSubscription) {
                        $message = "Du har fått begränsat godkännande i vår kreditupplysning. Givetvis vill vi gärna att du blir kund hos oss. Vi kan istället erbjuda dig följande:";
                        $product_ids = $this->getDeviceSubscriptionCodeGN1Post();
                        $aCollection['error_message'] = $message;
                        //$aCollection['product_ids'] = $product_ids; // According to TELE-2174
                        $aCollection['subscription'] = $this->_downgradeSubscription;
                        return $aCollection;
                    }
                    $this->_downgradeSubscription = $this->getSubscriptionCodeA();
                    if ($this->_downgradeSubscription) {
                        return $this->_downgradeSubscription;
                    }
            } elseif ($filter['error_code'] == "CREDIT_CHECK_FIRST_LEVEL_VALID") {
                    $message = "Du har fått begränsat godkännande i vår kreditupplysning. Givetvis vill vi gärna att du blir kund hos oss. Vi kan istället erbjuda dig följande:";
                    $this->_downgradeSubscription = $this->getSubscriptionCodeA();
                    //$product_ids = $this->getDeviceSubscriptionCodeGN1Pre();
                    $aCollection['error_message'] = $message;
                    //$aCollection['product_ids'] = $product_ids;
                    $aCollection['subscription'] = $this->_downgradeSubscription;
                    return $aCollection;
            }
        }
        return false;
    }
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
    
    protected function _getLastOrder()
    {
        $lastId =  Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastId);
        return $order;
    }

    public function getLastSubscriptionGroup()
    {
        $aSubscriptionGroup = array();
        $aSubscriptionType = array();
        $aCollection = array();
        $aDeviceType = array();
        $items = $this->_getLastOrder()->getAllVisibleItems();
        //set default group for show popup of downgrade in the checkout cart
        $aSubscriptionGroup[] = 1;

        if (count($items)) {
            foreach ($items as $item)
            {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_DEVICE || $attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_DONGLE) {
                    $_subscription = Mage::helper('tele2_subscription/data')->getSubscriptionBySku($item->getSku());
                    $aSubscriptionGroup[] = $_subscription->getSubscriptionGroup()->getId();
                    $aSubscriptionType[] = $_subscription->getType1();
                    $aDeviceType[] = $attributeSetName;
                } elseif ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION) {
                    $_subscription = Mage::getModel('tele2_subscription/mobile')->getSubscriptionByProductId($item->getProduct()->getId());
                    $aSubscriptionGroup[] = $_subscription->getSubscriptionGroup()->getId();
                    $aSubscriptionType[] = $_subscription->getType1();
                    $aDeviceType[] = $attributeSetName;
                }
            }
        }
        $aCollection['groups'] = array_unique($aSubscriptionGroup);
        $aCollection['type'] = array_unique($aSubscriptionType);
        $aCollection['device_type'] = $aDeviceType;
        return $aCollection;
    }

    public function getLastSubscriptionType()
    {
        $item = $this->_getLastOrder()->getAllVisibleItems()->getFirstItem();
        $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
        if ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_DONGLE) {
            $_subscription = Mage::helper('tele2_subscription/data')->getSubscriptionBySku($item->getSku());
            return $_subscription->getType1();
        }
    }
}