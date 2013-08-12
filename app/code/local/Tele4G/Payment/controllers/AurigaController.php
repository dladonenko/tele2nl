<?php

require ROOT_PATH.'app/code/core/Mage/Checkout/controllers/CartController.php';
class Tele4G_Payment_AurigaController extends Mage_Checkout_CartController
{
    protected $_model;
    public function getAuriga()
    {
        if (is_null($this->_model))
            $this->_model = Mage::getModel('tele4G_payment/auriga');
        return $this->_model;
    }

    public function indexAction()
    {
        $aurigaRequestParameters = $this->getAuriga()->getAurigaRequestParameters();
        if ($aurigaRequestParameters) {
            $aurigaRequestData = $this->getAuriga()->getAurigaRequestData($aurigaRequestParameters);
            if ($aurigaRequestData) {
                $session = Mage::getSingleton('checkout/session');
                $session->setAurigaQuoteId($session->getQuoteId());
                $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_redirect')->toHtml());
                Mage::log("aurigaAction - Request Data SS4 ".print_r($aurigaRequestData, true)."", Zend_Log::INFO, 'payment-controller.log');
            } else {
                Mage::log("aurigaAction - Error: SS4 ".print_r($aurigaRequestData, true)."", Zend_Log::INFO, 'payment-controller.log');
                $this->getAuriga()->loadFailureData('*');
                $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_failure')->toHtml());
            }
        } else {
            Mage::log("aurigaAction - Error: SS4 ".print_r($aurigaRequestParameters, true)."", Zend_Log::INFO, 'payment-controller.log');
            $this->getAuriga()->loadFailureData('*');
            $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_failure')->toHtml());
        }
        Mage::log("aurigaAction - Send Request to SS4: ".print_r($aurigaRequestParameters, true)."", Zend_Log::INFO, 'payment-controller.log');
        Mage::log("aurigaAction - Request Data from SS4: ".print_r($aurigaRequestData, true)."", Zend_Log::INFO, 'payment-controller.log');
    }

    public function  responseAction()
    {
        $client = Mage::helper('tele4G_sS4Integration')->getClient();
        $session = Mage::getSingleton('checkout/session');
        
        Mage::log('responseAction - Auriga $_REQUEST '.print_r($_REQUEST, true)."", Zend_Log::INFO, 'payment-controller.log');

        if( $_REQUEST['Status']=='A' && $_REQUEST['Status_code']==0 ){
            Mage::log("responseAction - Auriga Status A Status_COde 0 ".print_r($_REQUEST, true)."", Zend_Log::INFO, 'payment-controller.log');
            if ($client) {

                $auriga_quote_id = $session->getAurigaQuoteId(true);
                $params = new stdClass();
                $params->validateAurigaPaymentAttemptRequest = array('parameters' => array(
                    'version' => $_REQUEST['Version'],
                    'customerRefNo' => $_REQUEST['Customer_refno'],
                    'transactionId' => $_REQUEST['Transaction_id'],
                    'status' => $_REQUEST['Status'],
                    'statusCode' => $_REQUEST['Status_code'],
                    'authCode' => $_REQUEST['AuthCode'],
                    'threeDSec' => $_REQUEST['3DSec'],
                    'currency' => $_REQUEST['Currency'],
                    'paymentMethod' => $_REQUEST['Payment_method'],
                    'cardNum' => $_REQUEST['Card_num'],
                    'expDate' => $_REQUEST['Exp_date'],
                    'cardType' => $_REQUEST['Card_type'],
                    'riskScore' => $_REQUEST['Risk_score'],
                    'issuingBank' => $_REQUEST['Issuing_bank'],
                    'ipCountry' => $_REQUEST['IP_country'],
                    'issuingCountry' => $_REQUEST['Issuing_country'],
                    'authorizedAmount' => $_REQUEST['Authorized_amount']
                ), 'expectedMAC' => $_REQUEST['MAC']);

                Mage::log("responseAction - Auriga Error validateAurigaPaymentAttemptRequest ".print_r($params, true)."", Zend_Log::INFO, 'payment-controller.log');
                $aurigaPaymentAttempt = $client->validateAurigaPaymentAttempt($params);
                if ($aurigaPaymentAttempt->validateAurigaPaymentAttempt->result == "OK") {
                    Mage::log("responseAction - Auriga Payment OK ".print_r($aurigaPaymentAttempt, true)."", Zend_Log::INFO, 'payment-controller.log');
                    // In case of parallel order processing, use RefNr from Auriga
                    //$order = Mage::getModel('sales/order');
                    //$order_id = $_REQUEST['Customer_refno'];
                    //$order->loadByIncrementId($order_id);

                    $customerRefNum = $_REQUEST['Customer_refno'];
                    $_orderCollection = Mage::getResourceModel('sales/order_collection')
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('auriga_customer_ref_num', $customerRefNum)
                        ->setPage(1, 1);
                    foreach ($_orderCollection as $order);

                    $session->setQuoteId( $auriga_quote_id );
                    $session->getQuote()->setIsActive(false)->save();
                    $order->sendNewOrderEmail();
                    $status = $order->getStatus();
                    // Save transaction_id in private attribute field
                    $order->setData( "auriga_transaction_id", $_REQUEST['Transaction_id'] );
                    $state = Mage_Sales_Model_Order::STATE_PROCESSING;
                    $msg = $this->__("Order created") . ":<br>". $this->__("Auriga Ref") . ": <b>" . $_REQUEST["Transaction_id"] . "</b>";
                    $order->setState($state, 'processing', $msg, true);

                    Mage::log("responseAction - added status+msg to history: $status", Zend_Log::INFO, 'payment-controller.log');
                    $order->save();
                    $response = Mage::getModel('tele4G_sS4Integration/sS4Integration')->creatOrder($order);
                    Tele4G_Sales_Model_Observer::saveOrderResponse($order, $response);
                    $successOrder = Mage::helper('tele4G_checkout')->isSuccessOrder();
                    if ($successOrder) {
                        $redirectUrl = Mage::getUrl('checkout/onepage/success');
                    } else {
                        Mage::log("order: {$order->getIncrementId()}", 1, "auriga.unsuccess.log");
                        $redirectUrl = Mage::getUrl('checkout/tele4G/unsuccess');
                    }
                    Mage::app()->getFrontController()->getResponse()->setRedirect($redirectUrl);
                    //$this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_success')->toHtml());
                } else {
                    $customerRefNum = $_REQUEST['Customer_refno'];
                    $order = $this->_getOrder($customerRefNum);
                    $this->_setActiveQuote($order);

                    $this->getAuriga()->loadFailureData('BAD_MAC');
                    $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_failure')->toHtml());

                    Mage::log("responseAction - Auriga Error aurigaPaymentAttempt ".print_r($aurigaPaymentAttempt, true)."", Zend_Log::INFO, 'payment-controller.log');
                }
            } else{
                Mage::log("responseAction - Auriga CLient SS4 error", Zend_Log::INFO, 'payment-controller.log');
            }
        } else {
            $customerRefNum = $_REQUEST['Customer_refno'];
            $order = $this->_getOrder($customerRefNum);
            $code = $_REQUEST['Status'].$_REQUEST['Status_code'];

            $message = $this->getAuriga()->loadFailureData($code);
            $this->_updateOfferData($order, $code, $message);

            $this->_setActiveQuote($order);
            Mage::log("responseAction - Auriga COde Error ".print_r($code, true)."", Zend_Log::INFO, 'payment-controller.log');

            $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_failure')->toHtml());
        }
    }

    private function _updateOfferData($order, $code, $message)
    {
        $offerData = $order->getOfferData();
        if ($offerData) {
            $offerData = unserialize($offerData);
        } else {
            $offerData = array();
        }
        $result = array(
            'ss4_order' => array(
                'error_code' => $code,
                'errorName' => $message,
                'ss4_order_id' => '',
            )
        );
        $offerData = $offerData + $result;
        $order->setOfferData(serialize($offerData))->save();
    }

    public function cancelAction()
    {
        Mage::log("cancelAction - begin", "payment-controller.log" );
        $session = Mage::getSingleton('checkout/session');
		
		$order = Mage::getModel('sales/order');
        $customerRefNum = $_REQUEST['Customer_refno'];

		$order->loadByAttribute('auriga_customer_ref_num', $customerRefNum);
		$order->cancel();
		$status_pay_canceled = "canceled";
		$order->setStatus( $status_pay_canceled );
		$order->save();
        
        $this->getAuriga()->loadFailureData("CanceledPayment");

        $this->_setActiveQuote($order);

        $session->setQuoteId($session->getAurigaQuoteId(true));
        $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/auriga_cancel')->toHtml());
        Mage::log("cancelAction - end", "payment-controller.log" );
    }
    
    private function _setActiveQuote($order)
    {
        $session = Mage::getSingleton('checkout/session');

        $quote = Mage::getModel('sales/quote')
                    ->load($order->getQuoteId());
        //Return quote
        if ($quote->getId()) {
            $quote->setIsActive(1)
                ->setReservedOrderId(NULL)
                ->save();
            $session->replaceQuote($quote);
        }
        $session->unsLastRealOrderId();
    }
    
    private function _getOrder($customerRefNum)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByAttribute('auriga_customer_ref_num', $customerRefNum);
        return $order;
    }
}