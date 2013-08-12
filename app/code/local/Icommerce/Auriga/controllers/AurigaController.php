<?php


/**
 * Auriga Checkout Controller
 */
class Icommerce_Auriga_AurigaController extends Mage_Core_Controller_Front_Action
{
	protected $_order;
	protected $_callbackAction = false;
	
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    protected function throwException( $action, $msg ){
        Icommerce_Default::logAppend( "throwException - $action - $msg", "var/auriga/auriga-controller.log" );
    }

    public function testAction()
    {
        $order = Mage::getModel('sales/order')->load(16);
        $order->setData( "auriga_transaction_id","test_id" );
        $order->save();
    }

    public function redirectAction()
    {
        Icommerce_Default::logAppend( "redirectAction - begin", "var/auriga/auriga-controller.log" );
        $session = Mage::getSingleton('checkout/session');
        // #?
        $session->setAurigaQuoteId($session->getQuoteId());
		Mage::app()->getResponse()->setHeader("Content-Type", "text/html; charset=ISO-8859-1",true); 
        $this->getResponse()->setBody($this->getLayout()->createBlock('auriga/redirect')->toHtml());
        $session->unsQuoteId();
        Icommerce_Default::logAppend( "redirectAction - end", "var/auriga/auriga-controller.log" );
    }
    
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }


    public function cancelAction()
    {
        Icommerce_Default::logAppend( "cancelAction - begin", "var/auriga/auriga-controller.log" );
        $auriga = Mage::getModel('auriga/auriga');
        $session = Mage::getSingleton('checkout/session');
		
		$order = Mage::getModel('sales/order');
		try { 
			$order_id = $_REQUEST['Customer_refno'];
		} catch( Exception $e ){
			$order_id = $session->getLastRealOrderId();
		}
		$order->loadByIncrementId( $order_id );
		$order->cancel();
		$status_pay_canceled = "canceled";
		$order->setStatus( $status_pay_canceled );
		$order->save();
		
        $session->setQuoteId($session->getAurigaQuoteId(true));
        $this->getResponse()->setBody($this->getLayout()->createBlock('auriga/cancel')->toHtml());
        Icommerce_Default::logAppend( "cancelAction - end", "var/auriga/auriga-controller.log" );
    }

    public function  successAction()
    {
        Icommerce_Default::logAppend( "successAction - begin", "var/auriga/auriga-controller.log" );
		$this->throwException( "success", "We use response instead of success action" );
        Icommerce_Default::logAppend( "successAction - end", "var/auriga/auriga-controller.log" );
    }

    /* public function callbackAction() { } */

	// Come here after initial payment
    public function  responseAction()
    {
        Icommerce_Default::logAppend( "responseAction - begin", "var/auriga/auriga-controller.log" );
        $auriga = Mage::getModel('auriga/auriga');
        $session = Mage::getSingleton('checkout/session');
		
		// Check the MAC - first build MAC string
		$mac_str = "";
		try {
			$mac_str .= $_REQUEST['Merchant_id'];
			$mac_str .= $_REQUEST['Version'];
			$mac_str .= $_REQUEST['Customer_refno'];
			$mac_str .= $_REQUEST['Transaction_id'];
			$mac_str .= $_REQUEST['Status'];
			$mac_str .= $_REQUEST['Status_code'];
			$mac_str .= $_REQUEST['AuthCode'];
			$mac_str .= $_REQUEST['3DSec'];
			$mac_str .= $_REQUEST['Batch_id'];
			$mac_str .= $_REQUEST['Payment_method'];
			$mac_str .= $_REQUEST['Card_type'];
			$mac_str .= $_REQUEST['Risk_score'];
			$mac_get = $_REQUEST['MAC'];
		} catch( Exception $e ){
			$this->throwException( "response", "Invalid Auriga payment response (1)." );
		}

		// Now calculate it
		$Key = $auriga->getConfigData('hemligt_ord'); //"35a25b9e-074d-4dfe-95f9-3bb7bd567575";
		$mac = md5( $mac_str . $Key );
		if( $mac!= $mac_get ){
            $aur_stat = $_REQUEST['Status'];
            $aur_code = $_REQUEST['Status_code'];
			$this->throwException( "response", "Invalid Auriga payment response (2). (Aur_stat:$aur_stat  Aur_code:$aur_code  Key:$Key  mac_str:$mac_str  mac:$mac  mac_get:$mac_get)" );
		}
		
		// In case of parallel order processing, use RefNr from Auriga
		$order = Mage::getModel('sales/order');
		$order_id = $_REQUEST['Customer_refno'];
		$order->loadByIncrementId( $order_id );
		
		// Success?
		if( $_REQUEST['Status']=='A' && $_REQUEST['Status_code']==0 ){
			// Yes
			try { 
				$auriga_quote_id = $session->getAurigaQuoteId(true);
			} catch( Exception $e ){
				$x = 0;
			}
			$session->setQuoteId( $auriga_quote_id );
			$session->getQuote()->setIsActive(false)->save();
			
			try { 
                Icommerce_Default::logAppend( "responseAction - sending email...", "var/auriga/auriga-controller.log" );
				$order->sendNewOrderEmail();
                Icommerce_Default::logAppend( "responseAction - sent email", "var/auriga/auriga-controller.log" );
			} catch( Exception $e ){
				$x = 0;
			}
			
			// If order status is still in initial phase (Pending)
			// we should move it forward now:
			$status = $order->getStatus();
			$captured = false;
			if( !$auriga->getConfigData('direct_capture') ){
				// Capture later
				$state = Mage_Sales_Model_Order::STATE_NEW;
				$status_new = $auriga->getConfigData('order_status_reserved');
			}
			else {
				$state = Mage_Sales_Model_Order::STATE_PROCESSING;
				$status_new = $auriga->getConfigData('order_status_captured');
				$captured = true;
			}
		
			// Save transaction_id in private attribute field
			$order->setData( "auriga_transaction_id", $_REQUEST['Transaction_id'] );
			$msg = $this->__("Order created") . ":<br>". $this->__("Auriga Ref") . ": <b>" . $_REQUEST["Transaction_id"] . "</b>";
			//$order->addStatusToHistory($status,$msg);
            $order->setState($state,$status_new,$msg,true);
            Icommerce_Default::logAppend( "responseAction - added status+msg to history: $status", "var/auriga/auriga-controller.log" );
			
			$order->save();

            // Dispatch event
            
	    
			$this->getResponse()->setBody($this->getLayout()->createBlock('auriga/success')->toHtml());
		}
		else {
			// No, Auriga payment failed
            Icommerce_Default::logAppend( "responseAction - Auriga payment failed, status:".$_REQUEST['Status']." Status_code:".$_REQUEST['Status_code'], "var/auriga/auriga-controller.log" );
			$status_pay_failed = $auriga->getConfigData('order_status_pay_failed');
            // After payment fails there is not so much we can do to advance again
            if( $status_pay_failed=="pay_failed" || 
                $status_pay_failed=="canceled" ){
                $order->cancel();
            }
			$order->setStatus( $status_pay_failed );
			$status = $order->getStatus();
			$order->save();

			$this->getResponse()->setBody($this->getLayout()->createBlock('auriga/failure')->toHtml());
		}
        Icommerce_Default::logAppend( "responseAction - end", "var/auriga/auriga-controller.log" );
    }

	// Come here after a capture attempt
	public function captureresponseAction( ){
        Icommerce_Default::logAppend( "captureresponseAction - begin", "var/auriga/auriga-controller.log" );
        $auriga = Mage::getModel('auriga/auriga');
        $session = Mage::getSingleton('checkout/session');
		
		// Check the MAC - first build MAC string
		$mac_str = "";
		try {
			$mac_str .= $_REQUEST['Merchant_id'];
			$mac_str .= $_REQUEST['Version'];
			$mac_str .= $_REQUEST['Customer_refno'];
			$mac_str .= $_REQUEST['Transaction_id'];
			$mac_str .= $_REQUEST['Status'];
			$mac_str .= $_REQUEST['Status_code'];
			$mac_str .= $_REQUEST['AuthCode'];
			$mac_str .= $_REQUEST['3DSec'];
			$mac_str .= $_REQUEST['Batch_id'];
			$mac_str .= $_REQUEST['Payment_method'];
			$mac_str .= $_REQUEST['Card_type'];
			$mac_str .= $_REQUEST['Risk_score'];
			$mac_get = $_REQUEST['MAC'];
		} catch( Exception $e ){
			$this->throwException( "captureresponse", "Invalid Auriga payment response (3)." );
		}

		// Now calculate it
		$Key = $auriga->getConfigData('hemligt_ord'); //"35a25b9e-074d-4dfe-95f9-3bb7bd567575";
		$mac = md5( $mac_str . $Key );
		if( $mac!= $mac_get ){
			$this->throwException( "captureresponse", "Invalid Auriga captureresponse (4)." );
		}
		
		// In case of parallel order processing, use RefNr from Auriga
		$order = Mage::getModel('sales/order');
		$increment_id = $_REQUEST['Customer_refno'];
		$order->loadByIncrementId( $increment_id );
		
		// Success?
		if( $_REQUEST['Status']=='A' && $_REQUEST['Status_code']==0 ){
			// Yes
			$status = $order->getStatus();
			$msg = $this->__("Successful Auriga capture") . ":<br>". $this->__("Auriga Ref") . ": <b>" . $_REQUEST["Transaction_id"] . "</b>";
			$order->addStatusToHistory($status,$msg);
			$order->save();
          		//$this->getResponse()->setBody($this->getLayout()->createBlock('auriga/success')->toHtml());
		}
		else {
			// No, Auriga payment failed
			$status_pay_failed = $auriga->getConfigData('order_status_pay_failed');
            // After payment fails there is not so much we can do to advance again
            if( $status_pay_failed=="pay_failed" || 
                $status_pay_failed=="canceled" ){
                $order->cancel();
            }
            
			$msg = $this->__("Failed Auriga capture") . "(".$_REQUEST['Status'].":".$_REQUEST['Status_code'].")" . ":<br>". $this->__("Auriga Ref") . ": <b>" . $_REQUEST["Transaction_id"] . "</b>";
			$order->addStatusToHistory($status_pay_failed,$msg);
			$order->save();
		}
		
		// Redirect - with hack to import "old" URL (generated on admin side)
		$order_id = $order->getData("entity_id");
		$url = Mage::getModel("adminhtml/url")->getUrl( 'admin/sales_order/view', array('order_id' => $order_id) );
		try {
			$path = Mage::getBaseDir()."/var/auriga_nxt_$order_id";
			$url = trim( file_get_contents( $path ) );
		} catch( Exception $e ){ }
		
		//$this->_redirect( 'admin/sales_order/view', array('order_id' => $order_id) );
		//$this->_redirect( $url );
		$this->getResponse()->setRedirect( $url );
        Icommerce_Default::logAppend( "captureresponseAction - end", "var/auriga/auriga-controller.log" );
	}
}
