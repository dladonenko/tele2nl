<?php
class Tele4G_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_product = null;
    private $_summaryCount = null;
    private $_productOfferId = null;

    public function getLeastTotalCostByOfferId($offer_id, $quote_items)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if (!empty($quote_items))
        {
            $leastTotalCost = 0;
            $wrappingLeastTotalCost = 0;

            foreach($quote_items as $_item)
            {
                $wrappingLeastTotalCost = 0;
                if ($_item->getOfferId() == $offer_id) {
                    $product = Mage::getModel('catalog/product')->load($_item->getProductId());
                    $attributeSetName = $this->_getAttributeSetNameById($product->getAttributeSetId());
                    if ($attributeSetName == $helperCommon::ATTR_SET_DEVICE && $_item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                        if (preg_match('%subscr-(\d+)-(\d+)%',  $_item->getSku(), $foundSBT)) {
                            if (is_array($foundSBT)) {
                                $_subscriptionId = $foundSBT[1];
                                $_bindPeriod = $foundSBT[2];
                                $_product_subscription = Mage::getModel('tele2_subscription/mobile')->load($_subscriptionId);
                                if ($_product_subscription->getType1() == $_product_subscription::SUBSCRIPTION_TYPE1_PRE && $_bindPeriod == 0) {
                                    $wrappingLeastTotalCost = 0;
                                } else {
                                    $wrappingLeastTotalCost = ($_item->getPrice() + $_product_subscription->getPrice() * $_bindPeriod);
                                }
                            }
                        }
                    } elseif ($attributeSetName == $helperCommon::ATTR_SET_ADDON) {
                        $wrappingLeastTotalCost = 0;
                    } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                        //$wrappingLeastTotalCost = $product->getMonthlyPrice();
                        $wrappingLeastTotalCost = ($_item->getPrice() + ($product->getMonthlyPrice() * 0));
                        //$wrappingBasePrice = $product->getMonthlyPrice();
                    } elseif ($attributeSetName == $helperCommon::ATTR_SET_ACCESSORY) {
                        $wrappingLeastTotalCost = $_item->getPrice();
                    } elseif ($attributeSetName == $helperCommon::ATTR_SET_INSURANCE) {
                        $wrappingLeastTotalCost = $_item->getPrice();
                    }
                    $leastTotalCost += $wrappingLeastTotalCost;
                }
            }
        }
        return $_item->getStore()->convertPrice($leastTotalCost, false);
    }
    
    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }

	public function getSubscriptionIdByproduct($id){
        $subscription = Mage::getModel('tele2_subscription/mobile')->load($id, 'fake_product_id');
        if($subscription && $subscription->getSubscriptionId()){
            $resultId = $subscription->getSubscriptionId();
        } else{
            $resultId = false;
        }
        return $resultId;
	}

    public function getSummaryCount()
    {
        if (is_null($this->_summaryCount)) {
            $quote = Mage::helper('checkout/cart')->getCart()->getQuote();
            $this->_summaryCount = count($quote->getAllVisibleItems());
        }
        return $this->_summaryCount;
    }

    public function isSuccessOrder()
    {
        $orderIncrementId = Mage::getSingleton('checkout/type_onepage')->getLastOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $offerData = unserialize($order->getOfferData());
        if (
            (isset($offerData['ss4_order']['ss4_order_id']) && $offerData['ss4_order']['ss4_order_id'] == $orderIncrementId) ||
            (isset($offerData['ss4_order']['status']) && $offerData['ss4_order']['status'] == 'OK')
        ) {
            $order->sendNewOrderEmail(true);
            return true;
        } else {
            //$errorMsg = ''
            //    .((isset($offerData['ss4_order']['error_code']) && $offerData['ss4_order']['error_code'] != '0') ? $offerData['ss4_order']['error_code'] .": " : '')
            //    .(isset($offerData['ss4_order']['status']) ? $offerData['ss4_order']['status']."\n" : '')
            //    .(isset($offerData['ss4_order']['errorName']) ? $offerData['ss4_order']['errorName']."\n" : '')
            //    .(isset($offerData['ss4_order']) ? print_r($offerData['ss4_order'], 1)."\n" : '');
//            $errorMsg = "";
//            if (isset($offerData['ss4_order']['errorName'])) {
//                $xml = simplexml_load_file('docs/ErrorCodeList.xml');
//                if (
//                    $xml->{$offerData['ss4_order']['errorName']} &&
//                    $xml->{$offerData['ss4_order']['errorName']} != ''
//                ){
//                    $errorMsg = (string) $xml->{$offerData['ss4_order']['errorName']};
//                } else {
//                    $errorMsg = (string) $offerData['ss4_order']['errorName'];
//                }
//            }
//            if ($errorMsg) {
//                Mage::getSingleton('core/session')->addError($errorMsg);
//            } else {
//                Mage::getSingleton('core/session')->addError('Other error.');
//            }
            return false;
        }
    }

    public function getPrepaidBinding()
    {
        $quoteItems = Mage::getModel('checkout/session')->getQuote()->getAllItems();
        foreach ($quoteItems as $_item) {
            $attSetId = $_item->getProduct()->getAttributeSetId();
            if (
                $this->_getAttributeSetNameById($attSetId) == 'device'
                //&&
                //$_item->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE    
            ) {
                /*foreach ($_item->getOptions() as $_otion) {
                    echo "<pre>".print_r($_option, 1)."</pre>";
                }*/
                //echo "PRE";
                //echo "<pre>".print_r($_item->getData(), 1)."</pre>";
                return 6;
            }
        }
    }

    /**
     * @param null $order
     * @return string
     */
    public function getExpectedDeliveryTimeFromOrder($order = null)
    {
        $days = "dagar";
        $sExpectedDeliveryTime = "1-3 {$days}";
        if ($order instanceof Mage_Sales_Model_Order) {
            $expectedDeliveryTime = array();
            $orderItems = $order->getItemsCollection();
            foreach ($orderItems as $_item) {
                if ($_item->getParentItem()) continue;
                $_sExpectedDeliveryTime = (string)$_item->getExpectedDeliveryTime();
                preg_match("/[\-]*([\d]{1})[\s]*([a-zA-Z]+)/", $_sExpectedDeliveryTime, $result);
                if (isset($result[1]) && isset($result[2])) {
                    if ($result[2] == $days) {
                        $expectedDeliveryTime[(int)$result[1]] = $_sExpectedDeliveryTime;
                    } else {
                        $expectedDeliveryTime[(int)$result[1] * 7] = $_sExpectedDeliveryTime;
                    }
                }
            }
            if (count($expectedDeliveryTime)) {
                krsort($expectedDeliveryTime);
                $sExpectedDeliveryTime = array_shift($expectedDeliveryTime);
            }
        }
        return $sExpectedDeliveryTime;
    }

    public function isShowMonthlyPrice($subscription)
    {
        if (($subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE 
                || $subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE) && $subscription->getPrice() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Check is client assistant.
     * Response mocked for White Label site
     *
     * @return bool
     */
    public function isAssistant()
    {
        /*if (Mage::getModel('core/cookie')->get('COP')) {
            return true;
        } else {
            return false;
        }*/
        return true;
    }

    public function getIsFmcgOnly()
    {
        return Mage::getModel('tele4G_checkout/cart')->getIsFmcgOnly();
    }
    
    public function getPaymentError($payment_method, $order)
    {
        switch ($payment_method) {
            case "tele4G_invoice":
            case "tele4G_downpayment":
                return $this->getPaymentKlarnaError($order);
            break;
        }
        return '';
    }
    
    public function getPaymentKlarnaError($order = null)
    {
        if (!$order) {
            return null;
        }
        $klarnaErrors = array(
            "ERROR_CREATING_INVOICE", 
            "ERROR_CREATING_INVOICE_ESTORE_OVERRUN"
        );
        $offerData = unserialize($order->getOfferData());
        //$offerData['ss4_order']['errorName'] = "ERROR_CREATING_INVOICE"; //for testing
        if (isset($offerData['ss4_order']['errorName'])) {
            $error_name = $offerData['ss4_order']['errorName'];
            if (in_array($error_name, $klarnaErrors)) {
                return 'klarnaError';
            }
        }
        return '';
    }
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function restoreQuoteById($quoteId = null)
    {
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        if ($quote->getId()) {
            $quote->setIsActive(1)->setReservedOrderId(NULL)->save();
            $this->_getCheckout()->replaceQuote($quote);
            $this->_getCheckout()->unsLastRealOrderId();
        }
    }

    public function duplicateQuoteById($oldQuoteId = null, $withQuoteItems = false)
    {
        if ($oldQuoteId) {
            $quote = Mage::getModel('sales/quote')->load($oldQuoteId);
            $this->_getCheckout()->replaceQuote($quote);
            $productOffer = Mage::getModel('tele4G_checkout/offer')->getProductOfferFromQuote();
            $this->_productOfferId = $productOffer->getOfferId();
            //$payment = $quote->getPayment();
            //$billingAddress = $quote->getBillingAddress();
            //$shippingAddress = $quote->getShippingAddress();
            //$allItems = $quote->getItemsCollection();

            $quote->setIsActive(1)->setId(null)->setReservedOrderId(NULL);
            $quote->save();
            $quoteId = $quote->getId();
            unset($quote);
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            //$quote->setPayment($payment);
            //$quote->setBillingAddress($billingAddress);
            //$quote->setShippingAddress($shippingAddress);
            /*if ($withQuoteItems) {
                foreach($allItems as $item) {
                    $item->setId(null)->setQuoteId($quoteId)->save();
                }
            }*/
            $this->_getCheckout()->replaceQuote($quote);
            $this->_getCheckout()->unsLastRealOrderId();
            return true;
        }
        return false;
    }

    protected function _getLastOrder()
    {
        $lastId =  $this->_getCheckout()->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastId);
        return $order;
    }

    public function replaceProductInCart($productId)
    {
        $lastOrder = $this->_getLastOrder();
        $this->duplicateQuoteById($lastOrder->getQuoteId());

        $observer = new Tele4G_Checkout_Model_Observer;
        $observer->changeOfferId();
        $storeId = Mage::app()->getStore()->getStoreId();
        $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
        Mage::log("\n ssn: (" . $lastOrder->getSsn() . "): \n selected product (" . $productId . "): " . $product->getName(), Zend_Log::INFO, 'downgrade.log');
        // now for only subscription product
        if (Mage::helper('tele2_catalog')->isSubscription($product)) {
            $cart = Mage::getSingleton('checkout/cart');
            $cart->addProduct($product);
            $cart->save();
            $tele4GOffer = Mage::getModel('tele4G_checkout/offer');
            $lastItem = $tele4GOffer->getLastQuoteItem();
            $tele4GOffer->setProductDataToQuoteItem($lastItem, $product);
            $lastItem->setOfferId($this->_productOfferId);
            $lastItem->save();
            return true;
        }
        return false;
    }

}
