<?php

class Tele4G_Checkout_Model_Observer
{
    /**
     * Sales Quote Save Before observer event
     */
    public function saveQuoteBefore($observer)
    {
        $quote = $observer->getQuote();

        $items = $quote->getAllVisibleItems();
        if (!count($items)) {
            return $this;
        }

        $this->_countMonthlyPrice($quote);
    }

    protected function _getRequest() 
    {
        return Mage::app()->getRequest();
    }

    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }

    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Returns SS4 purchase assistant cookie value to store it in order data
     * @todo: move cookie name to config
     * @return string
     */
    
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * This method save monthly price and least total cost in quote
     */
    protected function _countMonthlyPrice($quote)
    {
        $items = $quote->getAllVisibleItems();

        $monthlyPrice = 0;
        $baseMonthlyPrice = 0;
        
        $leastTotalCost = 0;
        $wrappingBasePrice = 0;
        $wrappingLeastTotalCost = 0;
        
        foreach($items as $item)
        {
            $wrappingBasePrice = 0;
            $wrappingLeastTotalCost = 0;
            $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());

            $_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

            if (($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_DEVICE && $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) ||
                $attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_DONGLE) {

                $_subscription = Mage::helper('tele2_subscription/data')->getSubscriptionBySku($item->getSku());
                $wrappingBasePrice = $_subscription->getPrice() * $item->getQty();
                $wrappingLeastTotalCost = ($item->getPrice() + $_subscription->getPrice() * $_subscription->getParamBindPeriod());

            } elseif ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_ADDON) {
                    $wrappingBasePrice = $_product->getMonthlyPrice();
                    $wrappingLeastTotalCost = 0;
            } elseif ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION) {
                $wrappingLeastTotalCost = ($_product->getPrice() + ($_product->getMonthlyPrice() * 0));
                $wrappingBasePrice = $_product->getMonthlyPrice();
            } elseif ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_ACCESSORY) {
                $wrappingBasePrice = 0;
                $wrappingLeastTotalCost = 0;
            } elseif ($attributeSetName == Tele4G_Common_Helper_Data::ATTR_SET_INSURANCE) {
                $wrappingLeastTotalCost = $_product->getPrice();
                $wrappingBasePrice = 0;
            }

            $leastTotalCost += $wrappingLeastTotalCost;

            $baseMonthlyPrice += $wrappingBasePrice;
            $monthlyPrice += $quote->getStore()->convertPrice($wrappingBasePrice, false);
        }

        $quote->setLeastTotalCost($leastTotalCost);
        $quote->setMonthlyPriceAmount($monthlyPrice);
        $quote->setBaseMonthlyPriceAmount($baseMonthlyPrice);

        foreach ($quote->getAllAddresses() as $address)
        {
            $address->setMonthlyPriceAmount($monthlyPrice);
            $address->setBaseMonthlyPriceAmount($monthlyPrice);
            
            $address->setLeastTotalCost($leastTotalCost);
        }
    }
    
    public function saveTogoResellerAndCity()
    {
        $productOffer = Mage::getModel("tele4G_checkout/offer")->getProductOfferFromQuote();
        if ($productOffer) {
            $post = false;
            $catalogHelper = Mage::helper('tele2_catalog');
            if ($catalogHelper->isDeviceOrDongle($productOffer->getProduct())) {
                $subscriptionHelper = Mage::helper('tele2_subscription');
                $subscription = $subscriptionHelper->getSubscriptionBySku($productOffer->getSku());
                if ($subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                    $post = true;
                }
            } elseif ($catalogHelper->isSubscription($productOffer->getProduct())) {
                if ($productOffer->getProduct()->getAttributeText('subscription_type') === 'post') {
                    $post = true;
                }
            }
            if ($post) {
                if (Mage::app()->getRequest()->getParam('usetogo')) {
                    $resellerToGo = Mage::app()->getRequest()->getParam('resellerToGo');
                    $citiesToGo = Mage::app()->getRequest()->getParam('citiesToGo');
                    $this->_getCheckout()->setSelectedReseller($resellerToGo);
                    $this->_getCheckout()->setSelectedResellerCity($citiesToGo);
                }
            }
        }
    }
    
    public function changeOfferId()
    {
        $attributeSetToFilter = array(
            Tele4G_Common_Helper_Data::ATTR_SET_DEVICE,
            Tele4G_Common_Helper_Data::ATTR_SET_DONGLE,
            Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION
        );
        $quote = $this->_getCheckout()->getQuote();
        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
            if (in_array($attributeSetName, $attributeSetToFilter)) {
                $offerId = $item->getOfferId();
                $this->_getCheckout()->setOfferId($offerId);
                $param = $this->_getCheckout()->getOfferParamsAfterCart();
                $param['product'] = $item->getProductId();
                $this->_getCheckout()->setOfferParamsAfterCart($param);
                return $offerId;
            }
        }
        return null;
    }

}