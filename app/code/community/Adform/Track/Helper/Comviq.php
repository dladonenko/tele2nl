<?php

class Adform_Track_Helper_Comviq extends Adform_Track_Helper_Data
{
    const ATTR_SET_DEVICE = "device";
    const ATTR_SET_DONGLE = "dongle";
    const ATTR_SET_SUBSCRIPTION = "subscription";
    const ATTR_SET_ADDON = "addon";
    const ATTR_SET_ACCESSORY = "accessory";
    const ATTR_SET_INSURANCE = "insurance";

    private $_order = null;
    private $_ssn = null;
    protected $_attributeSetNames;

    private function _getOrder()
    {
        if (!$this->_order) {
            $lastId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $this->_order = Mage::getModel('sales/order')->load($lastId);
        }
        return $this->_order;
    }

    private function _getSsn()
    {
        if(!$this->_ssn) {
            $this->_ssn = $this->_getOrder()->getSsn();
        }
        return $this->_ssn;
    }

    /**
     * Retrive Country name
     * 
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getCountry($order = null)
    {
        $countryName = "SWEDEN";
        //if ($order) {
        //    $countryName = $order->getBillingAddress()->getCountry();
        //}
        return $countryName;
    }

    /**
     * getAgeGroup
     *
     * @param type $ssn
     * @return string
     */
    public function getAgeGroup($ssn = null)
    {
        if (!$ssn) {
            $ssn = $this->_getSsn();
        }

        $ageGroups = array(
            7 => "A",
            17 => "B",
            30 => "C",
            40 => "D",
            50 => "E",
            60 => "F",
            70 => "G",
            80 => "H"
        );

        $groupName = "UNKNOWN";

        $age = $this->getAge($ssn);

        foreach ($ageGroups as $key => $group) {
            if ($age > $key) {
                $groupName = $group;
            }
        }
        return $groupName;
    }

    /**
     * getAge
     *
     * @param type $ssn
     * @return integer
     */
    public function getAge($ssn = null)
    {
        if (!$ssn) {
            $ssn = $this->_getSsn();
        }

        $age = '';
        if ($ssn) {
            $customerBirthYear =  (int) substr($ssn, 0, 4);
            $customerBirthMonth = (int) substr($ssn, 4, 2);
            $customerBirthDay   = (int) substr($ssn, 6, 2);
            $age = (date("Y") - $customerBirthYear);
            if ($customerBirthMonth > date("m")) {
                $age--;
            } elseif (
                $customerBirthMonth == date("m") &&
                $customerBirthDay > date("d")
            ) {
                $age--;
            }
        }

        return $age;
    }

    /**
     * getGenderFromSsn
     *
     * @param type $ssn
     * @return string
     */
    public function getGenderFromSsn($ssn = null)
    {
        if (!$ssn) {
            $ssn = $this->_getSsn();
        }

        $genderDigit = (int) substr($ssn, strlen($ssn) - 2, 1);
        $gender = "";
        if ($genderDigit % 2 == 0)
        {
            $gender = "K";// It's even
        }
        else
        {
            $gender = "M";// It's odd
        }
        return $gender;
    }

    public function getPhoneNumber($item)
    {
        $order = $this->_getOrder();
        if ($order && $offerData = unserialize($order->getOfferData())) {
            if (isset($offerData[$item->getOfferId()]) && isset($offerData[$item->getOfferId()]['number'])) {
                return $offerData[$item->getOfferId()]['number'];
            }
        }
    }

    public function getOfferValue($item)
    {
        $trackingCategory = null;

        if (!$item->getParentItem()) {
            $attributeSetName = $this->_attributeSetNames[$item->getProduct()->getAttributeSetId()]->getAttributeSetName();

            $offerDate = unserialize($this->_getOrder()->getOfferData());
            $activationType = (isset($offerDate[$item->getOfferId()]['type']) ? $offerDate[$item->getOfferId()]['type'] : "");

            if ($attributeSetName == self::ATTR_SET_DEVICE) {
                $_subscription = $this->_subscriptions[$item->getSubscriptionIdFromSku()];
                if ($_subscription->getType1() == Tele2_Subscription_Model_Subscription::SUBSCRIPTION_TYPE1_PRE) {
                    //activation type
                    if ($activationType == "PORT") {
                        $trackingCategory = "Comviq Voice Portering";
                    } else {
                        $trackingCategory = "Comviq Voice";
                    }

                } else if ($_subscription->getType1() == Tele2_Subscription_Model_Subscription::SUBSCRIPTION_TYPE1_POST){
                    if ($activationType == "PORT") {
                        $trackingCategory = "PostPaid Comviq Voice Portering";
                    } else {
                        $trackingCategory = "PostPaid Comviq Voice";
                    }
                }

            } elseif ($attributeSetName == self::ATTR_SET_DONGLE) {
                $trackingCategory = "Comviq Surf";
            } elseif ($attributeSetName == self::ATTR_SET_SUBSCRIPTION) {
                $_subscription = $this->_getSubscriptionFake($item->getProduct()->getId());
                if ($_subscription->getType1() == Tele2_Subscription_Model_Subscription::SUBSCRIPTION_TYPE1_BB_PRE) {
                    $trackingCategory = "Comviq Surf SIM";
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Subscription::SUBSCRIPTION_TYPE1_PRE) {
                    if ($activationType == "PORT") {
                        $trackingCategory = "Comviq Voice Portering";
                    } else {
                        $trackingCategory = "Comviq Voice";
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Subscription::SUBSCRIPTION_TYPE1_POST) {
                    if ($activationType == "PORT") {
                        $trackingCategory = "PostPaid Comviq Voice Portering";
                    } else {
                        $trackingCategory = "PostPaid Comviq Voice";
                    }
                }
            }
        }
        return $trackingCategory;
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
     * _setAttributeSetNameByItems
     * 
     * @param type $_items
     */
    public function getAttributeSetNameByItems($attributeSetId)
    {
        if (is_null($this->_attributeSetNames)) {
            $this->_attributeSetNames = Mage::getModel('eav/entity_attribute_set')
                ->getCollection()
                ->load()
                ->getItems();
        }
        if (isset($this->_attributeSetNames[$attributeSetId])) {
            return $this->_attributeSetNames[$attributeSetId]->getAttributeSetName();
        } else {
            return '';
        }
    }
    
    public function prepareOfferValues($items)
    {
        $this->_setAttributeSetNameByItems($items);
        $this->_setSubscriptionsByItems($items);
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
            $this->_subscriptions = Mage::getModel('tele2_subscription/subscription')
                ->getCollection()
                ->addFieldToFilter('subscription_id', array('in' => $_subscriptions))
                ->load()
                ->getItems();
        }
        
        if (count($_subscriptionsFake)) {
            $this->_subscriptionsFake = Mage::getModel('tele2_subscription/subscription')
                ->getCollection()
                ->addFieldToFilter('fake_product_id', array('in' => $_subscriptionsFake))
                ->load()
                ->getItems();
        }
    }

    public function checkDevice($item)
    {
        if (
            $item->getTypeId() == 'simple' && 
            Mage::helper('adform_track/comviq')->getAttributeSetNameByItems($item->getAttributeSetId()) == self::ATTR_SET_DEVICE
        ) {
            return false;
        } else {
            return true;
        }
    }
}
