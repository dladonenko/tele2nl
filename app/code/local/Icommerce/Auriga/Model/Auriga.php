<?php


// Setup code
// Check that if we're on >= M1.4 that we have created necessary DB columns
if( Icommerce_Db::tableExists("sales_flat_order") ){
    if( !Icommerce_Db::columnExists("sales_flat_order","auriga_transaction_id") ){
        Icommerce_Db::addColumn("sales_flat_order","auriga_transaction_id","varchar",64);
    }
    if( !Icommerce_Db::columnExists("sales_flat_order","auriga_captured") ){
        Icommerce_Db::addColumn("sales_flat_order","auriga_captured","int");
    }
}


// Auriga takes Latin1 encoded strings
function auriga_encode($s)
{
    // Truncate at new line
    if (($p = strpos($s, "\n")) !== FALSE) {
        $s = substr($s, 0, $p);
    }
    if (($p = strpos($s, "\r")) !== FALSE) {
        $s = substr($s, 0, $p);
    }
    return utf8_decode($s);
}

/**
 * Auriga Payment Block
 *
 */
class Icommerce_Auriga_Model_Auriga extends Mage_Payment_Model_Method_Abstract
{
    //const CGI_URL = 'https://epayment.auriganet.eu/paypagegw';
    //const CGI_URL_TEST = 'https://test-epayment.auriganet.eu/paypagegw';
    
    protected $_code = 'auriga';
    protected $_formBlockType = 'icommerce_auriga_block_form';
    static protected $_allowCurrencyCode = array('CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'NOK', 'SEK', 'USD');
    static protected $_allowLanguages = array("SWE", "FIN", "DAN", "NOR", "ENG", "ESP", "FRA", "ITA", "DEU", "NLD");
    static protected $_allowLanguages_iso2 = array("SWE" => "SE", "FIN" => "FI", "DAN" => "DK", "NOR" => "NO", "ENG" => "GB", "ESP" => "ES", "FRA" => "FR", "ITA" => "IT", "DEU" => "DE");
    static protected $_allowLanguages_iso3 = array("SE" => "SWE", "FI" => "FIN", "DK" => "DAN", "NO" => "NOR", "GB" => "ENG", "ES" => "ESP", "FR" => "FRA", "IT" => "ITA", "DE" => "DEU", "NL" => "NLD");
    
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Fontis_Australia_Model_Payment_Paymate
     */
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
    
    public function showTrustedList() {
    	$logoArray = explode(',', $this->getConfigData('showlogos'));
      foreach($logoArray as $item) {
      	if ($item == 'DIBS' ||
      		$item == 'VISA_SECURE' ||
      		$item == 'MC_SECURE' ||
      		$item == 'JCB_SECURE' || 
      		$item == 'PCI') {
      		return true;
      	} 
      }
      return false;
    }
    
    public function getPaymentText()
    {
	return $this->getConfigData('payment_text');
    }
    
    public function showCardsList() {
    	$logoArray = explode(',', $this->getConfigData('showlogos'));
      foreach($logoArray as $item) {
      	if ($item == 'AMEX' ||
      		$item == 'BAX' ||
      		$item == 'DIN' || 
      		$item == 'DK' || 
      		$item == 'FFK' || 
      		$item == 'JCB' || 
      		$item == 'MC' || 
      		$item == 'MTRO' || 
      		$item == 'MOCA' || 
      		$item == 'VISA' || 
      		$item == 'ELEC' || 
      		$item == 'AKTIA' || 
      		$item == 'DNB' ||
      		$item == 'EDK' ||
      		$item == 'ELV' || 
      		$item == 'EW' || 
      		$item == 'FSB' || 
      		$item == 'GIT' || 
      		$item == 'ING' || 
      		$item == 'SEB' || 
      		$item == 'SHB' ||
      		$item == 'SOLO' ||   
      		$item == 'VAL') {
      		return true;	
      	} 
      }
      return false;
    }
    
    public function isTest()
    {
        $it = $this->getConfigData('api_test');
        return $it;
    }
    
    public function getUsername()
    {
        return $this->getConfigData('username');
    }
    
    protected function getFailureURL()
    {
        $url = $this->getConfigData('url_cancel');
        return $url;
    }
    
    /*protected function getAcceptURL ()
    {
    $url = $this->getConfigData('url_accept');
    return $url;
    }
    
    protected function getCallbackURL ()
    {
    $url = $this->getConfigData('url_callback');
    return $url;
    }*/
    
    public function getUrl()
    {
        if ($this->isTest()) {
            return $this->getConfigData('cgi_url_test');
        } else {
            return $this->getConfigData('cgi_url');
        }
    }
    
    
    /**
     * Get session namespace
     *
     * @return Fontis_Australia_Model_Payment_Paymate_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('auriga/auriga_session');
    }
    
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Icommerce_Default::getCheckoutQuote();
        //return $this->getCheckout()->getQuote();
    }
    
    public function getFinalSuccessUrl()
    {
        return Mage::getUrl('checkout/onepage/success');
    }
    
    public function getFinalFailureURL()
    {
        return Mage::getUrl('checkout/onepage/failure');
    }
    
    public function priceToStr($fp)
    {
        $s = sprintf("%.2f", $fp);
        return str_replace('.', ',', $s);
    }
    
    static public function formatAmount($cost)
    {
        return (int) ($cost * 100);
    }
    
    public function buildMac(&$fields)
    {
        // Build the MAC string
        $MAC_string = "";
        foreach ($fields as $k => $v) {
            $MAC_string .= $v;
        }
        $Key = $this->getConfigData('hemligt_ord'); //"35a25b9e-074d-4dfe-95f9-3bb7bd567575";
        $MAC_string .= $Key;
        $MAC_hash      = md5($MAC_string);
        $fields['MAC'] = $MAC_hash;
        return $this;
    }
    
    
    public function getLastOrderId()
    {
        $read           = Mage::getSingleton('core/resource')->getConnection('core_read');
        $entity_type_id = 4; // Hard coded for "sales/order"
        $store_id       = Mage::app()->getStore()->getData("store_id");
        $rr             = $read->query("SELECT increment_last_id FROM eav_entity_store WHERE entity_type_id='$entity_type_id' AND store_id='$store_id';");
        $r              = $rr->fetch();
        return $r['increment_last_id'];
    }
    
    
    public function getCheckoutFormFields()
    {
        // Convert order to invoice - this will set status to order_status
        $order = Mage::getModel('sales/order');
        
        $last_real_order_id = $this->getCheckout()->getLastRealOrderId();
        $order->loadByIncrementId($last_real_order_id);
        $convertor = Mage::getModel('sales/convert_order');
        $invoice   = $convertor->toInvoice($order);
        
        $quote = $this->getQuote();
        $items = $quote->getItemsCollection();
        
        // customer only works for logged in ones, not for guests
        $cust = Mage::getSingleton('customer/session')->getCustomer();
        
        $Description = "";
        foreach ($items as $item) {
            //$this->printClass($item);
            $snxt = auriga_encode($item->getQty() . " " . $item->getName());
            if (strlen($Description) + strlen($snxt) > 400) {
                break;
            }
            if ($Description)
                $Description .= ", ";
            $Description .= $snxt;
        }
        
        $addr      = $this->getQuote()->getShippingAddress();
        $bill_addr = $this->getQuote()->getBillingAddress();
        $ship_addr = $this->getQuote()->getShippingAddress();
        if (!$ship_addr) {
            $ship_addr = $bill_addr;
        }
        $totals = $this->getQuote()->getTotals();
        $tcost  = $cost = $totals['grand_total']->getValue();
        $ecost  = array_key_exists('shipping', $totals) ? $totals['shipping']->getValue() : 0;
        $cost -= $ecost;
        $tax = array_key_exists('tax', $totals) ? $totals['tax']->getData("value") : 0;
        
        $Merchant_id       = $this->getConfigData('merchant_id'); //"2838";
        $Customer_refno    = $order->getData("increment_id");
        //$Customer_refno = $this->getLastOrderId();
        $Currency          = $this->getQuote()->getBaseCurrencyCode();
        $Amount            = self::formatAmount($tcost);
        // ### Get correct VAT
        $VAT               = self::formatAmount($tax);
        $Payment_method    = "PAYPAGE"; // $this->getConfigData('payment_method');
        $Purchase_date     = date("YmdHi");
        $Response_URL      = Mage::getUrl('auriga/auriga/response');
        //$Goods_description = auriga_encode("Köp från vendor - ...");
        $store_name        = utf8_decode(Mage::app()->getStore()->getName());
        $Goods_description = $store_name;
        $Capture_now       = $this->getConfigData('direct_capture') ? "YES" : "NO";
        $cb                = $bill_addr->getCountry();
        $cs                = $ship_addr->getCountry();
        // Default to swedish
        $Language          = array_key_exists($cb, self::$_allowLanguages_iso3) ? self::$_allowLanguages_iso3[$cb] : "SWE";
        $Country           = array_key_exists($cs, self::$_allowLanguages_iso3) ? $cs : "SE";
        $Cancel_URL        = Mage::getUrl('auriga/auriga/cancel');
        $days              = $this->getConfigData("invoice_days");
        if (!$days)
            $days = 14;
        $Last_dayofpayment = date("Ymd", time() + $days * 24 * 60 * 60); // Using invoice_days
        
        $fields = array(
            'Merchant_id' => $Merchant_id,
            'Version' => 2,
            'Customer_refno' => $Customer_refno,
            'Currency' => $Currency,
            'Amount' => $Amount,
            'VAT' => $VAT,
            'Payment_method' => $Payment_method,
            'Purchase_date' => $Purchase_date,
            'Response_URL' => $Response_URL,
            'Goods_description' => $Goods_description,
            'Language' => $Language,
            'Capture_now' => $Capture_now,
            'Comment' => $Description,
            'Country' => $Country,
            'Cancel_URL' => $Cancel_URL,
            'Exclude_method' => $this->getConfigData('exclude_method'),
            'Exclude_card' => $this->getConfigData('exclude_card'),
            'OCR_number' => '',
            'Personal_identity' => '',
            'Last_dayofpayment' => $Last_dayofpayment,
            'Name' => auriga_encode($bill_addr->getFirstname() . " " . $bill_addr->getLastname()),
            'Street_address' => auriga_encode($bill_addr->getData("street")),
            'Complementary_address' => auriga_encode($bill_addr->getData("region")),
            'City_address' => auriga_encode($bill_addr->getData("city")),
            'Postalcode_address' => auriga_encode($bill_addr->getData("postcode")),
        );
        
        $this->buildMac($fields);
        
        // $fields['Capture_now'] = $Capture_now;
	    
        // Log this request
        Icommerce_Log::writeSeqFile(Mage::getBaseDir("var") . "/auriga", "auriga", $fields);
        
        return $fields;
    }
    
    // Make sure we initialize to correct state and status
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
    
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('auriga/auriga_form', $name)->setMethod('auriga')->setPayment($this->getPayment())->setTemplate('icommerce/auriga/form.phtml');
        
        return $block;
    }
    
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code, self::$_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('sales')->__('Selected currency code (' . $currency_code . ') is not compatabile with Auriga'));
        }
        return $this;
    }
    
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        $payment = 1;
    }
    
    public function canCapture()
    {
        // We don't have a given order here, so we cannot check if it has been captured.
        // Use the configuration setting as an approximation for this
        return $this->getConfigData('direct_capture') ? false : true;
        //$order->getData( "auriga_captured" );
        //return true;
    }
    
    /*public function capture( Varien_Object $payment, $amount ){
    $order = $payment->getOrder();
    $status = $order->getStatus();
    $status_reserved = $this->getConfigData( 'order_status_reserved' );
    //if( $status!==$status_reserved ){
    //	// throw ...?
    //  return false;
    //}
    
    $order_id = $order->getData("entity_id");
    $increment_id = $order->getData("increment_id");
    
    // Get fields
    $fields = array( 
    "Merchant_id" => $this->getConfigData('merchant_id'),
    "Version" => 2,
    "Customer_refno" => $increment_id,
    "Amount" => $this->formatAmount( $order->getData("grand_total") ),
    "VAT" => $this->formatAmount( $order->getData("tax_amount") ),
    // ## We really need our own return URL that can process the capture result
    "Response_URL" => Mage::getBaseUrl() . "auriga/auriga/captureresponse",
    //"Response_URL" => Mage::helper('adminhtml')->getUrl( "*"."/sales_order/view", array("order_id"=>$order_id) ),
    "Request_type" => "Confirm",
    "Delivery_date" => date("Ymd"),  
    );
    
    // Build MAC
    $this->buildMac( $fields );
    
    $url = $this->getUrl();
    // Chop off last bit of URL
    $url = preg_replace( "|(https://[\w\.\-]+/).+|i","$1admingw", $url );
    
    // Now send this with curl - we get problems
    //$ch = curl_init( $url );
    //$r = curl_setopt( $ch, CURLOPT_POST, true );
    //$r = curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    //$r = curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
    
    // And run it		
    //$out = curl_exec( $ch );
    //curl_close( $ch );
    
    $html = "<html><body> \n";
    $html .= "  <form action='$url' id='auriga_capture' > \n";
    foreach( $fields as $k => $v ){
    if( 1  ){
    //if( $k!=="MAC" ){
    //$html .= "    $k: <input type='hidden' name='$k' value='$v' > <br>\n";
    $html .= "    <input style='display:none;' type='hidden' name='$k' value='$v' > <br>\n";
    }
    }
    //$html .= "    <input type='submit' value='s' > \n";
    $html .= "  </form> \n";
    $html .= "  <script type='text/javascript'> \n";
    $html .= "    var t = setTimeout('document.getElementById(\"auriga_capture\").submit()',0); \n";
    $html .= "  </script> \n";
    $html .= "</html></body> \n";
    $_POST['REPLACEMENT_HTML'] = $html;
    
    // This is a hack to get the right forward URL on return from Auriga
    $url = mage::getSingleton( "adminhtml/url" )->getUrl( '*'.'/sales_order/view', array('order_id' => $order_id) );
    $path = Mage::getBaseDir()."/var/auriga_nxt_$order_id";
    $base_url = Mage::getBaseUrl();
    //$url = substr($url,strlen($base_url));
    file_put_contents( $path, $url );
    }*/
    
    public function capture(Varien_Object $payment, $amount)
    {
        require_once("Icommerce/Auriga/auriga_soap.php");
        
        $order          = $payment->getOrder();
        $status         = $order->getStatus();
        $order_id       = $order->getData("entity_id");
        $increment_id   = $order->getData("increment_id");
        $transaction_id = $order->getData("auriga_transaction_id");
        
        if ($order->getData("auriga_captured")) {
            // throw ...;  ?
            return false;
        }
        
        $merchant_id = $this->getConfigData('merchant_id');
        $hemligt_ord = $this->getConfigData('hemligt_ord');
        $aur_soap    = new aurigaWs($merchant_id, $hemligt_ord);
        //$r = $aur_soap->capture( $increment_id );
        $r           = $aur_soap->confirm($transaction_id, date("Ymd"), $this->isTest());
        
        // Success?
        if (!is_string($r) || strpos($r, "not") === FALSE) {
            $order->setData("auriga_captured", 1);
            return true;
        } else {
            return false;
        }
    }
    
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('auriga/auriga/redirect');
    }
}
