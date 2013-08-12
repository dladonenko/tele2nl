<?php

require ROOT_PATH.'app/code/core/Mage/Checkout/controllers/CartController.php';
class Tele4G_Payment_DibsController extends Mage_Checkout_CartController
{
    protected $_model;
    protected $_checkout;

    public function getModel()
    {
        if (!$this->_model) {
            $this->_model = Mage::getModel('tele4G_payment/dibs');
        }
        return $this->_model;
    }

    protected function _getCheckout()
    {
        if (!$this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }
    
    public function indexAction()
    {
        $dibsRequestParameters = $this->getModel()->getDibsRequestParametersFromSS4();
        if ($dibsRequestParameters) {
            $this->_getCheckout()->setParamsForDibs($dibsRequestParameters);
//            $this->_getCheckout()->setDibsQuoteId($this->_getCheckout()->getQuoteId());
//            $this->_getCheckout()->setLastRealOrderId($this->_getCheckout()->getQuote()->);
            $this->getModel()->saveOrder();
            $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/dibs_redirect')->toHtml());
        } else {
            $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/dibs_failure')->toHtml());
        }
        
    }

    public function returnAction()
    {
        $redirectUrl = Mage::getUrl('checkout/cart');

        // validate Order at SS5
        if ($params = $this->getRequest()->getParams()) {
            Mage::log("response from DIBS\n".print_r($params, 1), 1, 'dibs.return.log');
            $response = $this->getModel()->validateDibsInSS4($this->getRequest()->getParams());

            if (
                isset($params['orderId']) && $params['orderId'] &&
                isset($params['status']) && $params['status'] == 'ACCEPTED'
            ) {
                $orderIncrementId = Tele4G_Payment_Model_Dibs::ORDER_PREFIX.$params['orderId'];
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
                //echo "params: \n".print_r($params, 1);
                //echo "response: \n".print_r($response, 1);
                // create Order at SS4
                if (
                    $order &&
                    isset($response->validateCreditCardPaymentAttemptResponse->responseStatus->errorCode) && 
                    !$response->validateCreditCardPaymentAttemptResponse->responseStatus->errorCode
                ) {
                    // Success
                    $order->sendNewOrderEmail();
                    if (isset($params['transaction']) && $params['transaction']) {
                        $order->setDibsCustomerRefNum($params['orderId']);
                        $order->setDibsTransactionId($params['transaction']);
                        $msg = $this->__("Order created") . ":<br>". $this->__("DIBS transaction") . ": <b>" . $params['transaction'] . "</b>";
                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', $msg, true);
                        $order->save();
                    }
                    
                    $response = Mage::getModel('tele4G_sS4Integration/sS4Integration')->creatOrder($order);
                    Tele4G_Sales_Model_Observer::saveOrderResponse($order, $response);
                    $successOrder = Mage::helper('tele4G_checkout')->isSuccessOrder();
                    if ($successOrder) {
                        $redirectUrl = Mage::getUrl('checkout/onepage/success');
                    } else {
                        Mage::log("order: {$order->getIncrementId()}", 1, "dibs.unsuccess.log");
                        $redirectUrl = Mage::getUrl('checkout/tele4G/unsuccess');
                    }
                } else {
                    // Error
                    //echo "Error";
                    $redirectUrl = Mage::getUrl('checkout/tele4G/unsuccess');
                }
            }
        }
        Mage::app()->getFrontController()->getResponse()->setRedirect($redirectUrl);
    }

    public function callbackAction()
    {
        Mage::log('callback action');
        Mage::log(array('response from DIBS' => $this->getRequest()->getParams()), 1, 'dibs.callback.log');
    }

    public function cancelAction()
    {
        Mage::log(array('response from DIBS' => $this->getRequest()->getParams()), 1, 'dibs.cancel.log');
        $orderIncrementId = Tele4G_Payment_Model_Dibs::ORDER_PREFIX.$this->getRequest()->getParam('orderId');

        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $order->cancel();
        $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
        $order->save();

        Mage::getSingleton('core/session')->addNotice("Om du inte vill betala med ditt kontokort kan du välja att få en faktura eller betala med postförskott.");

        $this->_setActiveQuote($order);

        $this->_getCheckout()->setQuoteId($order->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('tele4G_payment/dibs_cancel')->toHtml());
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
}
