<?php

class Tele4G_Payment_Model_Dibs extends Mage_Payment_Model_Method_Abstract
{
    const ORDER_PREFIX = 'DIBS_';
    protected $_code = 'tele4G_dibs';
    protected $_canAuthorize = false;
    protected $_canCapture = false;
    protected $_quote = null;
    protected $_chekout = null;
    protected $_paramsForDibs = null;

    /**
     * Authorize payment abstract method
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return false;
    }

    protected function _getCheckout()
    {
        if (!$this->_chekout) {
            $this->_chekout = Mage::getModel('checkout/session');
        }
        return $this->_chekout;
    }
            
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $quoteId = $this->_getCheckout()->getQuoteId();
            $this->_quote = Mage::getModel('sales/quote')
                ->setStoreId(Mage::app()->getStore()->getId());
            if ($quoteId){
                $this->_quote->load($quoteId);
            }
        }
        return $this->_quote;
    }

    public function getDibsRequestParametersFromSS4()
    {
        if (!$this->_paramsForDibs) {
            $totals = $this->_getQuote()->getTotals();
            $grandTotal = $totals['grand_total']->getValue();

            $this->_paramsForDibs = array(
                array('key' => 'parameter', 'name' => 'amount',          'value' => (int)($grandTotal*100)),
                array('key' => 'parameter', 'name' => 'acceptReturnUrl', 'value' => Mage::getUrl($this->getConfigData('accept_return_url'), array('_secure'=>true))),
                array('key' => 'parameter', 'name' => 'callbackUrl',     'value' => Mage::getUrl($this->getConfigData('callback_url'), array('_secure'=>true))),
                array('key' => 'parameter', 'name' => 'cancelReturnUrl', 'value' => Mage::getUrl($this->getConfigData('cancel_url'), array('_secure'=>true))),
                array('key' => 'parameter', 'name' => 'currency',        'value' => $this->getConfigData('currency')),
                array('key' => 'parameter', 'name' => 'language',        'value' => $this->getConfigData('language')),
            );
            if ($this->getConfigData('api_test')) {
                $this->_paramsForDibs[] = 
                    array('key' => 'parameter', 'name' => 'test', 'value' => $this->getConfigData('api_test'));
            }
            $params = new stdClass();
            $params->creditCardPaymentAttemptRequest = array(
                //'customerInfo' => '198110019299',
                'provider'     => 'DIBS',
                'parameters'   => $this->_paramsForDibs,
            );

            $client = Mage::helper('tele4G_sS4Integration')->getClient();
            if ($client) {
                Mage::log("request params\n".print_r($this->_paramsForDibs, 1), 1, 'dibs.creditCardPaymentAttemptRequest.log');
                $response = $client->generateCreditCardPaymentAttemptData($params);

                if ($response && !$response->creditCardPaymentAttemptDataResponse->responseStatus->errorCode) {
                    Mage::log("request_xml\n".$client->__getLastRequest(), 1, 'dibs.creditCardPaymentAttemptRequest.log');
                    Mage::log("response\n".print_r($response, 1), 1, 'dibs.creditCardPaymentAttemptRequest.log');

                    $this->_paramsForDibs = array_merge($this->_paramsForDibs, array(
                        array('key' => 'parameter', 'name' => 'MAC',      'value' => $response->creditCardPaymentAttemptDataResponse->MAC),
                        array('key' => 'parameter', 'name' => 'merchant', 'value' => $response->creditCardPaymentAttemptDataResponse->merchantId),
                        array('key' => 'parameter', 'name' => 'orderId',  'value' => $response->creditCardPaymentAttemptDataResponse->reference)
                    ));
                    $this->_getQuote()
                        ->setReservedOrderId(self::ORDER_PREFIX.$response->creditCardPaymentAttemptDataResponse->reference)
                        ->save();
                }
            }
        }
        return $this->_paramsForDibs;
    }

    public function validateDibsInSS4($_params = array())
    {
        $client = Mage::helper('tele4G_sS4Integration')->getClient();
        if ($_params && count($_params) && $client) {
            Mage::log("post params from DIBS\n".print_r($_params, 1), 1, 'dibs.validateDibsInSS4.log');

            $_paramsForValidate = array();
            foreach ($_params as $_paramKey => $_paramValue) {
                $_paramsForValidate[] = array('key' => 'parameter', 'name' => $_paramKey, 'value' => $_paramValue);
            }
            $params = new stdClass();
            $params->creditCardPaymentInformation = array(
//                'customerInfo' => '198110019299',
                'provider'     => 'DIBS',
                'parameters'   => array('parameter' => $_paramsForValidate),
            );
            Mage::log("request params\n".print_r($params, 1), 1, 'dibs.validateDibsInSS4.log');
            $dibsResponse = $client->validateCreditCardPaymentAttempt($params);
            Mage::log("request_xml\n".$client->__getLastRequest(), 1, 'dibs.validateDibsInSS4.log');
            Mage::log(array('response from SS4' => $dibsResponse), 1, 'dibs.validateDibsInSS4.log');
            return $dibsResponse;
        }
        return false;
    }

    public function getDibsRequestParameters()
    {
        $fields = array();
        $params = $this->_getCheckout()->getParamsForDibs();
        if ($params && count($params)) {
            foreach($params as $param) {
                $fields[$param['name']] = $param['value'];
            }
        }
        return $fields;
    }

    public function saveOrder()
    {
        $lastOrderId = $this->_getCheckout()->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastOrderId);

        $convertor = Mage::getModel('sales/convert_order');
        $convertor->toInvoice($order);

        $orderIncrementId = $this->_getQuote()->getReservedOrderId();
        $order->setIncrementId($orderIncrementId);
        $order->save();
        
    }

}

