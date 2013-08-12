<?php

class Tele4G_Payment_Model_Auriga extends Mage_Payment_Model_Method_Abstract
{

    //private $response_url = "http://tele2.po.ciklum.net/payment/auriga/response/";
    //private $cancel_url = "http://tele2.po.ciklum.net/payment/auriga/cancel/";
    private $payment_method = "KORTINSE";
    private $version = 3;
    private $language = "SWE";
    private $country = "SE";
    protected $_code = 'tele4G_auriga';

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        if (Mage::helper('tele4G_checkout')->isAssistant()) {
            return false;
        } else {
            return parent::canUseCheckout();
        }
    }

    protected function _getQuote()
    {
        $checkout = Mage::getSingleton('checkout/session');
        $quote_id = $checkout->getQuoteId();
        $quote = Mage::getModel('sales/quote')
            ->setStoreId(Mage::app()->getStore()->getId());
        if( $quote_id ){
            $quote->load( $quote_id );
        }
        return $quote;
    }
    
    public function assignData($data)
    {
        $details = array();
        if ($this->getUsername()) {
            $details['username'] = $this->getUsername();
        }
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
    }
    
    public function isInitializeNeeded()
    {
        return true;
    }
    
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pay_pending');
        $stateObject->setIsNotified(false);
    }
    
    public function canCapture()
    {
        return $this->getConfigData('direct_capture') ? false : true;
    }
    
    public function getUsername()
    {
        return $this->getConfigData('username');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getAurigaRequestParameters()
    {
        $totals = $this->_getQuote()->getTotals();
        $total_cost  = $cost = $totals['grand_total']->getValue();
        $vat = $total_cost - $this->getQuotePriceWithoutVat();
        $ecost  = array_key_exists('shipping', $totals) ? $totals['shipping']->getValue() : 0;
        $cost -= $ecost;
        $tax = array_key_exists('tax', $totals) ? $totals['tax']->getData("value") : 0;

        $client = Mage::helper('tele4G_sS4Integration')->getClient();
        if ($client) {
            $params = new stdClass();
            $params->aurigaRequestParameters = array('version'=> $this->version,
                'currency'  => $this->_getQuote()->getBaseCurrencyCode(),
                //'amount'    => (int)self::formatAmount($total_cost),
                //'vat'       => (int)self::formatAmount($total_cost * 0.2),
                'amount'    => self::formatAmount($total_cost),
                'vat'       => self::formatAmount($vat),//self::formatAmount($tax), $total_cost * 0.2
                'paymentMethod' => $this->payment_method,
                'purchaseDate'  => date("YmdHi", strtotime($this->_getQuote()->getCreatedAt())),
                'responseURL'   => Mage::getBaseUrl().$this->getConfigData('response_url'),
                'goodsDescription'  => '',
                'language'  => $this->language,
                'comment'   => '',
                'country'   => $this->country,
                'cancelURL' => Mage::getBaseUrl().$this->getConfigData('cancel_url'),
            );
            $aurigaRequest = $client->getAurigaRequestData($params);
            return $aurigaRequest;
        }
        return false;
    }

    public function getAurigaRequestData($aurigaRequest)
    {
        $session = Mage::getSingleton('checkout/session');

        if (is_object($aurigaRequest)) {
            if ($aurigaRequest->aurigaRequestData->responseStatus->status == 'OK') {
                $session->setMerchantId($aurigaRequest->aurigaRequestData->merchantId);
                $session->setCustomerRefno($aurigaRequest->aurigaRequestData->aurigaReference);
                $session->setMac($aurigaRequest->aurigaRequestData->requestMAC);
                return $aurigaRequest;
            }
        }
        return false;
    }

    public function getCheckoutFormFields()
    {
        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order');
        $last_real_order_id = $this->getCheckout()->getLastRealOrderId();
        $order->loadByIncrementId($last_real_order_id);
        $order->setAurigaCustomerRefNum($session->getCustomerRefno());
        //$order->setIncrementId($session->getCustomerRefno());
        $convertor = Mage::getModel('sales/convert_order');
        $convertor->toInvoice($order);
        $order->save();
        
        $totals = $this->_getQuote()->getTotals();
        $total_cost  = $cost = $totals['grand_total']->getValue();
        $vat = $total_cost - $this->getQuotePriceWithoutVat();
        $ecost  = array_key_exists('shipping', $totals) ? $totals['shipping']->getValue() : 0;
        $cost -= $ecost;
        $tax = array_key_exists('tax', $totals) ? $totals['tax']->getData("value") : 0;

        $fields = array(
            'Merchant_id' => $session->getMerchantId(),
            'Version' => $this->version,
            'Customer_refno' => $session->getCustomerRefno(),
            'Currency' => $this->_getQuote()->getBaseCurrencyCode(),
            'Amount' => self::formatAmount($total_cost),
            'VAT' => self::formatAmount($vat), //self::formatAmount($total_cost * 0.2)  //self::formatAmount($tax),
            'Payment_method' => $this->payment_method,
            'Purchase_date' => date("YmdHi", strtotime($this->_getQuote()->getCreatedAt())),
            'Response_URL' => Mage::getBaseUrl().$this->getConfigData('response_url'),
            'Goods_description' => '',
            'Language' => $this->language,
            'Comment' => '',
            'Country' => $this->country,
            'Cancel_URL' => Mage::getBaseUrl().$this->getConfigData('cancel_url'),
            'MAC' => $session->getMac()
        );
        return $fields;
    }

    static public function formatAmount($cost)
    {
        return (int) ($cost * 100);
    }
    
    public function loadFailureData($code = "*")
    {
        $session = Mage::getSingleton('checkout/session');
        $error_model = Mage::getModel('tele4G_payment/aurigaerrors');
        $message = $error_model->getErrorMessage($code);
        $session->setAurigaErrorMessage($message);
        return $message;
    }
    
    public function getFinalFailureURL()
    {
        Mage::getSingleton('core/session')->addNotice($this->getFailuireMessage());
        return Mage::getUrl('checkout/tele4G');
    }
    
    public function getFailuireMessage()
    {
        $session = Mage::getSingleton('checkout/session');
        return $session->getAurigaErrorMessage();
    }
    
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('payment/auriga');
    }

    public function isTest()
    {
        $it = $this->getConfigData('api_test');
        return $it;
    }
    
    public function getUrl()
    {
        if ($this->isTest()) {
            return $this->getConfigData('cgi_url_test');
        } else {
            return $this->getConfigData('cgi_url');
        }
    }
    
    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }
    
    public function getQuotePriceWithoutVat()
    {
        $alternativeVatRate = Mage::getStoreConfig('tele4G/variables/alternative_vat_rate');
        $alternativeVatRateInsurance = Mage::getStoreConfig('tele4G/variables/alternative_vat_rate_for_insurance');
        $priceWithoutVat = 0;
        foreach ($this->_getQuote()->getAllVisibleItems() as $quoteItem) {
            $_additionalData = $quoteItem->getAdditionalData();
            $additionalData = unserialize($_additionalData);
            if (isset($additionalData['price_without_vat']) && $additionalData['price_without_vat'] > 0) {
                $priceWithoutVat += $additionalData['price_without_vat'];
            } elseif ($this->_getAttributeSetNameById($quoteItem->getProduct()->getAttributeSetId()) == Tele4G_Common_Helper_Data::ATTR_SET_INSURANCE) {
                $priceWithoutVat += $quoteItem->getPrice() * $alternativeVatRateInsurance;
            } else {
                $priceWithoutVat += $quoteItem->getPrice() * $alternativeVatRate;
            }
        }
        return $priceWithoutVat;
    }
}
