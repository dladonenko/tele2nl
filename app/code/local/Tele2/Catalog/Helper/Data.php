<?php
class Tele2_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * getAttributeSetNameById
     * @param attributeSetId $attributeSetId
     * @return string attributeSetName
     */
    public static function getAttributeSetNameById($attributeSetId = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($attributeSetId)->getAttributeSetName();
    }

    /**
     * Returns attributeset name for given product
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductAttributeSet(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId())->getAttributeSetName();
    }

    /**
     * Check (by attributesetid or product instance) if the product is a Device Or a Dongle
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isDeviceOrDongle($productOrAttributeSetId = null)
    {
        return ($this->isDevice($productOrAttributeSetId) || $this->isDongle($productOrAttributeSetId)) ? true : false;
    }

    /**
     * Check (by attributesetid or product instance) if the product is a Device
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isDevice($productOrAttributeSetId = null)
    {
        if ($productOrAttributeSetId instanceof Mage_Catalog_Model_Product) {
            $attributeSetId = $productOrAttributeSetId->getAttributeSetId();
        } else {
            $attributeSetId = $productOrAttributeSetId;
        }

        if ($this->getAttributeSetNameById($attributeSetId) == Tele2_Install_Helper_Data::ATTR_SET_DEVICE) {
            return true;
        }
        return false;
    }

    /**
     * Check (by attributesetid or product instance) if the product is a Dongle
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isDongle($productOrAttributeSetId = null) {
        if ($productOrAttributeSetId instanceof Mage_Catalog_Model_Product) {
            $attributeSetId = $productOrAttributeSetId->getAttributeSetId();
        } else {
            $attributeSetId = $productOrAttributeSetId;
        }

        if ($this->getAttributeSetNameById($attributeSetId) == Tele2_Install_Helper_Data::ATTR_SET_DONGLE) {
            return true;
        }
        return false;
    }

    /**
     * Check (by attributesetid or product instance) if the product is a Subscription
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isSubscription($productOrAttributeSetId = null)
    {
        if ($productOrAttributeSetId instanceof Mage_Catalog_Model_Product) {
            $attributeSetId = $productOrAttributeSetId->getAttributeSetId();
        } else {
            $attributeSetId = $productOrAttributeSetId;
        }

        if ($this->getAttributeSetNameById($attributeSetId) == Tele2_Install_Helper_Data::ATTR_SET_SUBSCRIPTION) {
            return true;
        }
        return false;
    }

    /**
     * Check (by attributesetid or product instance) if the product is a Addon
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isAddon($productOrAttributeSetId = null)
    {
        if ($productOrAttributeSetId instanceof Mage_Catalog_Model_Product) {
            $attributeSetId = $productOrAttributeSetId->getAttributeSetId();
        } else {
            $attributeSetId = $productOrAttributeSetId;
        }

        if ($this->getAttributeSetNameById($attributeSetId) == Tele2_Install_Helper_Data::ATTR_SET_ADDON) {
            return true;
        }
        return false;
    }
    
    /**
     * Check (by attributesetid or product instance) if the product is a Insurance
     * @param null $productOrAttributeSetId
     * @return bool
     */
    public function isInsurance($productOrAttributeSetId = null)
    {
        if ($productOrAttributeSetId instanceof Mage_Catalog_Model_Product) {
            $attributeSetId = $productOrAttributeSetId->getAttributeSetId();
        } else {
            $attributeSetId = $productOrAttributeSetId;
        }

        if ($this->getAttributeSetNameById($attributeSetId) == Tele2_Install_Helper_Data::ATTR_SET_INSURANCE) {
            return true;
        }
        return false;
    }

    public function getDefaultPreOption($subscription)
    {
        //options_179_896
        $highestBindingTime = 0;
        if (isset($subscription[Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE_PRE])) {
            foreach($subscription[Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE_PRE] as $pre) {
                if (is_array($pre)) {
                    foreach($pre['least_total_cost'] as $_bindingPeriod => $_bindCost) {
                        if ($_bindingPeriod > $highestBindingTime) {
                            $highestBindingTime = $_bindingPeriod;
                        }
                    }
                }
            }
            return 'options_'.$subscription['option_id'].'_'.$pre['value_ids'][$highestBindingTime];
        }
        return '';
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductSubscriptions(Mage_Catalog_Model_Product $product)
    {
        if ($this->getAttributeSetNameById($product->getAttributeSetId()) == Tele2_Install_Helper_Data::ATTR_SET_DEVICE ||
            $this->getAttributeSetNameById($product->getAttributeSetId()) == Tele2_Install_Helper_Data::ATTR_SET_DONGLE) {
            $product = Mage::getModel('catalog/product')->load($product->getId());
            $return = array();
            foreach ($product->getOptions() as $option) {
                if ($option->getDefaultTitle() == Tele2_Install_Helper_Data::CUSTOM_OPTION_SUBSCRIPTIONS) {
                    foreach ($option->getValues() as $value) {
                        $subscription = Mage::helper('tele2_subscription')->getSubscriptionIdBySky($value->getSku());
                        if ($subscription) {
                            if (!isset($return[$subscription->getSubscriptionId()])) {
                                $return[$subscription->getSubscriptionId()]['subscription'] = $subscription->getData();
                            }
                            if (preg_match('%subscr-(\d+)-(\d+)%',  $value->getSku(), $foundSubscription)) {
                                if (is_array($foundSubscription)) {
                                    $bindingPeriod = $foundSubscription[2];
                                    $return[$subscription->getSubscriptionId()]['subscription']['prices']['time_'.$bindingPeriod] = $product->getPrice() + $value->getPrice();
                                }
                            }
                        }
                    }

                }
            }
            return $return;
        }
        return array();
    }
}
