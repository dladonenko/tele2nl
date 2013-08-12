<?php
/**
 * Tele4G AllProducts View block
 *
 * @category   Tele4G
 * @package    Tele4G_Subscription
 */
class Tele4G_Catalog_Block_Category_View extends Mage_Catalog_Block_Product_Abstract
{
    protected function _getCatId()
    {
        if ($this->hasData('_cat_id')) {
            return $this->getData('_cat_id');
        }
        $catId = 0;
        if ($currentCategory = Mage::registry('current_category')) {
            $catId = $currentCategory->getId();
        } else {
            $catId = Mage::getModel('catalog/category')->loadByAttribute('code', 'configurable_devices')->getId();
        }
        $this->setData('_cat_id', $catId);
        return $catId;
    }
    
    public function getSubscriptionCollection()
    {
        if (is_null($this->_subscriptionCollection)) {
            $subscription = Mage::getModel('tele2_subscription/mobile');
            $this->_subscriptionCollection = Mage::getResourceModel('tele2_subscription/mobile_collection');
            if ($mode = $this->getRequest()->getParam('mode')) {
                if ('pre' == strtolower($mode)) {
                    $this->_subscriptionCollection
                        ->addFieldToFilter('type1', $subscription::SUBSCRIPTION_TYPE1_PRE);
                } elseif ('post' == strtolower($mode)) {
                    $this->_subscriptionCollection
                        ->addFieldToFilter('type1', $subscription::SUBSCRIPTION_TYPE1_POST);
                }
            }
        }
        return $this->_subscriptionCollection;
    }

    public function getDevicesBySubscriptionGroup($_catID, $mode = null)
    {
        $subscription = Mage::getModel('tele2_subscription/mobile');
        $deviceCollection = Mage::getModel('tele2_subscription/relation');

        if ('pre' == strtolower($mode)) {
            $type1 = $subscription::SUBSCRIPTION_TYPE1_PRE;
        } else {
            $type1 = $subscription::SUBSCRIPTION_TYPE1_POST;
        }

        $subscriptionType2Id = array($subscription::SUBSCRIPTION_TYPE2_S,$subscription::SUBSCRIPTION_TYPE2_M,$subscription::SUBSCRIPTION_TYPE2_L);
        $subscriptionType2Id = implode(',', $subscriptionType2Id); 
        $allProductsByGroups = $deviceCollection->getProductsBySubscriptionGroup($type1, $subscriptionType2Id, $_catID);
        $deviceGroupArray = array();
        
        foreach ($allProductsByGroups as $item){
            if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_S ){
                $deviceGroupArray[0][] = $item;
            } else if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_M){
                $deviceGroupArray[1][] = $item;
            } else if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_L){
                $deviceGroupArray[2][] = $item;
            }                    
        }

        return $deviceGroupArray;
    }
}
