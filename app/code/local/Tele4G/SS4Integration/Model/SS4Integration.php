<?php
/**
 * 
 */
class Tele4G_SS4Integration_Model_SS4Integration
{

    private $_orderTotalWithVat = 0;
    private $_orderTotalWithoutVat = 0;
    const CREATE_ORDER_LOG_FILE_XML = 'createOrders.log.xml';
    const CREATE_ORDER_LOG_FILE_CSV = 'createOrders.log.csv';
    const UNDEFINED = 'undefined';

    private $_carriers = array(
        'letter'                     => 'LETTER',
        'flatrate_flatrate'          => 'PACKAGE_WITH_ADVICE_NOTE',
        'freeshipping_freeshipping'  => 'PACKAGE_WITH_ADVICE_NOTE',
        'togo_togo'                  => 'COLLECT_IN_STORE',
        'none'                       => 'NONE',
        'default'                    => 'UNKNOWN_CARRIER',
    );

    private $_payments = array(
        'tele4G_invoice'         => 'invoicePaymentMethod',
        'tele4G_auriga'          => 'creditCardPaymentMethod',
        'tele4G_dibs'            => 'creditCardPaymentMethod',
        'tele4G_downpayment'     => 'invoicePartPaymentMethod',
        'tele4G_cashondelivery'  => 'cashOnDeliveryPaymentMethod',
        'default'                => 'UNKNOWN_PAYMENT',
    );

    private $_invoiceTypes = array(
        'factura_papper' => 'PAPER',
        'factura_epost'  => 'PDF',
        'default'        => 'UNKNOWN'
    );
    
    private $_productTypes = array(
        'subscription' => 'subscription',
        'addon'        => 'plusServiceArticle',
        'device'       => 'mobilePhoneArticle',
        'dongle'       => 'usbModemArticle',
        'accessory'    => 'accessoryArticle',
        'insurance'    => 'insuranceArticle',
        'default'      => 'UNKNOWN_PRODUCT',
    );

    private $_subscriptionTypes = array (
        'default'
            => 'UNKNOWN_SUBSCRIPTION_TYPE',
        Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE
            => 'MOBILE_VOICE_PREPAID',
        Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST
            => 'MOBILE_VOICE_POSTPAID',
        Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE
            => 'MOBILE_BROADBAND_PREPAID',
        Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST
            => 'MOBILE_BROADBAND_POSTPAID',
    );

    private $_attributeSetModel = null;
    private $_productModel = null;

    /**
     * Create Order at SS4 system
     * 
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function creatOrder($order)
    {
        $wsdlType = 'ss4';
        
        $xml = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:nl="http://nl.ss.tele2.se/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <soapenv:Header/>
   <soapenv:Body>
      <nl:createOrder>
         <!--Optional:-->
         <order>
            <shoppingContext>
               <shopId>1</shopId>
            </shoppingContext>
            <individual>
               <identificationNumber>123456</identificationNumber>
               <contactInfo>
                  <phone>0707898989</phone>
                  <email>someone@somewhere.nl</email>
               </contactInfo>
               <address>
                  <streetAddress>De Ruijterkade 111</streetAddress>
                  <city>Amsterdam</city>
                  <postalCode>1011 AB</postalCode>
               </address>
               <firstName>Albert</firstName>
               <lastName>Breithaus</lastName>
            </individual>
            <lineItems>
               <lineItem xsi:type="nl:subscription">
                  <lineItemNumber>1</lineItemNumber>
                  <id>123456</id>
                  <billplanCode>SIEBEL_PRODUCT_NAME</billplanCode>
                  <name>SIMonly 1GB</name>
                  <description>SIMonly 1GB Data bundle</description>
                  <activationType>NEW</activationType>
                  <bindingTime>0</bindingTime>
                  <phoneNumber>0707898989</phoneNumber>
                  <monthlyFee>
                     <withoutVat>7.92</withoutVat>
                     <withVat>9.90</withVat>
                  </monthlyFee>
                  <type>MOBILE_BROADBAND_POSTPAID</type>
                  <simCardType>MICRO_REGULAR</simCardType>
               </lineItem>
            </lineItems>
            <magentoOrderId>1234567</magentoOrderId>
            <paymentMethod xsi:type="nl:cashOnDeliveryPaymentMethod">
               <paymentFee>
                  <withVat>0.00</withVat>
                  <withoutVat>0.00</withoutVat>
               </paymentFee>
            </paymentMethod>
            <shipment>
               <shipmentType>LETTER</shipmentType>
               <shipmentFee>
                  <withVat>0.00</withVat>
                  <withoutVat>0.00</withoutVat>
               </shipmentFee>
            </shipment>
            <invoiceType>PAPER</invoiceType>
            <customerIpAddress>127.0.0.1</customerIpAddress>
            <total>
               <withVat>0.00</withVat>
               <withoutVat>0.00</withoutVat>
            </total>
         </order>
      </nl:createOrder>
   </soapenv:Body>
</soapenv:Envelope>
XML;

        $xmlElement = simplexml_load_string($xml, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");
        $xmlElement->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xmlElement->registerXPathNamespace('nl', 'http://nl.ss.tele2.se/');
        $xmlElement->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            
        $response = $this->sendRequest($wsdlType, $xmlElement);
        
        return $response;
    }
    
    public function sendRequest($wsdlType = 'ss4', $xmlElement)
    { 
        if ($wsdlType == 'togo') {
            $wsdlUrl = Mage::getStoreConfig('carriers/togo/wsdl');
        } elseif ($wsdlType == 'log') {
            $wsdlUrl = Mage::getStoreConfig('tele4G/ss4/wsdl_log');
        } else {
            $wsdlUrl = Mage::getStoreConfig('tele4G/ss4/wsdl');
        }
        if (!$wsdlUrl) {
            return false;
        }
        $ch = curl_init($wsdlUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlElement->asXml());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    public function addCommentToOrder($order, $xmlElement = null, $response = null)
    {
        if ($xmlElement) {
            $order->addStatusHistoryComment(print_r($xmlElement->asXml(),1), false);
        }
        if ($response) {
            $order->addStatusHistoryComment(print_r($response,1), false);
        }
        if ($xmlElement || $response) {
            $order->save();
        }
    }

    /**
     * Method return xml with success response.
     *
     * @param $order
     * @return string
     */
    protected function _getSuccessResponse($order)
    {
        $response = <<<RETURNXML
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <ns2:createOrderResponse xmlns:ns2="http://comviq.ss.tele2.se/">
                        <createOrderResponse>
                            <responseStatus>
                                <status>OK</status>
                                <errorCode>0</errorCode>
                            </responseStatus>
                            <orderId>{$order->getId()}</orderId>
                        </createOrderResponse>
                    </ns2:createOrderResponse>
                </soap:Body>
            </soap:Envelope>
RETURNXML;
        return $response;
    }


    /**
     * Add Create Order log info as xml to import it to excel
     * 
     * @param Mage_Sales_Model_Order $order
     * @param string $xmlRequest
     * @param string $xmlResponse
     * @return boolean
     */
    public function addLog($order = null, $xmlRequest = '', $xmlResponse = '', $asXML = false)
    {
        try {
            if (!($request = simplexml_load_string($xmlRequest)) && !($response = simplexml_load_string($xmlResponse)) && !$order ) {
                return false;
            }
            $request = $request->xpath('/soapenv:Envelope/soapenv:Body/com:createOrder/*');
            $request = array_shift($request);

            $response = $response->xpath('/soap:Envelope/soap:Body/*');
            $response = array_shift($response);
 
            $logDir  = Mage::getBaseDir('var') . DS . 'log';
            if ($asXML) {
                $logFile = $logDir . DS . self::CREATE_ORDER_LOG_FILE_XML;
                $defaultContent = '';
            } else {
                $logFile = $logDir . DS . self::CREATE_ORDER_LOG_FILE_CSV;
                $defaultContent = "time;orderid;ssn;email;firstname;lastname;phone_subscription;phone;"
                    ."order_value;payment_method;delivery_method;code;"
                    ."status;message;"
                    ."product;subscription;activation_type;sim_type;"
                    ."product;subscription;activation_type;sim_type\n";
            }

            if (!file_exists($logFile)) {
                file_put_contents($logFile, $defaultContent);
                chmod($logFile, 0777);
            }
//            if (!($xmlOrders = simplexml_load_string($logContent))) {
            if (!($xmlOrders = @simplexml_load_file($logFile))) {
                $xml = <<<XML
<orders/>
XML;
                $xmlOrders = simplexml_load_string($xml, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");
            }
            
//            Mage::log("request: \n".print_r($request, 1));
//            Mage::log("response: \n".print_r((array)$response, 1));
//            Mage::log("response: \n".(string)$response->createOrderResponse->createOrderResponse->responseStatus->status);
            if (isset($response->faultcode)) {
                $_code    = (string)$response->faultcode;
                $_status  = (string)$response->faultcode;
                $_message = (string)$response->faultstring;
            } elseif (isset($response->createOrderResponse)) {
                $_code    = (string)$response->createOrderResponse->responseStatus->errorCode;
                $_status  = (string)$response->createOrderResponse->responseStatus->status;
                if (isset($response->createOrderResponse->responseStatus->errorName)) {
                    $_message = (string)$response->createOrderResponse->responseStatus->errorName;
                } elseif (isset($response->createOrderResponse->orderId)) {
                    $_message = (string)$response->createOrderResponse->orderId;
                } else {
                    $_message = '';
                }
            } else {
                $_code    = 'UNKNOWN_STRUCTURE';
                $_status  = 'UNKNOWN_STRUCTURE';
                $_message = 'UNKNOWN_STRUCTURE';
            }
            
            $_subscription = array();
            $_product = array();
            $_activationType = array();
            $_simType = array();
            $_totalWithVat = 0;
            $_totalWithoutVat = 0;
            $j = 0;
            foreach ($request->lineItems->lineItem as $_item) {                 
                $att = (array)$_item->attributes();
                $att = $att['@attributes'];
                if (preg_match('/com:([a-z]*)/', (string)$att['type'], $matches)) { $j++;
                    $_totalWithVat += (string)$_item->upfrontPrice->withVat;
                    $_totalWithoutVat += (string)$_item->upfrontPrice->withoutVat;
                    //print_r($matches);
                    switch ($matches[1]) {
                        case 'subscription':
                            $_subscription[$j] = (string)$_item->name;
                            $_activationType[$j] = (string)$_item->activationType;
                            $_simType[$j] = (string)$_item->simCardType;                            
                            $phone = (string)$_item->phoneNumber;
                            break;
                        case 'mobile':
                            $_product[$j] = (string)$_item->description;
                            break;
                    }
                }
            } 
            $paymentLog = $this->_payments;  
            $paymentLog['default'] = 'EMPTY';  
            $paymentLog['tele4G_cashondelivery'] = 'CashOnDelivery';
            $paymentLog['tele4G_invoice'] = 'Klarna Invoice';
            $paymentLog['tele4G_dibs'] = 'DIBS Payment System';
            $paymentLog['tele4G_downpayment'] = 'Klarna Downpayment';
            $paymentLog['free'] = 'EMPTY';
            $paymentMethod = $paymentLog[$order->getPayment()->getMethod()];
            
            if ($request->shipment->shipmentType == "COLLECT_IN_STORE") {
                $paymentMethod = "ToGo";
            }
            if(empty($paymentMethod)){  
                $paymentMethod = 'EMPTY';  
            }             
            if(!isset($phone))
                $phone = 'EMPTY';            
            
            if ($asXML) {
                $xmlOrder = $xmlOrders->addChild('order');
                $xmlOrder->addChild('time',             $order->getCreatedAt());
                $xmlOrder->addChild('orderid',          $request->magentoOrderId);
                $xmlOrder->addChild('ssn',              $request->individual->identificationNumber);
                $xmlOrder->addChild('email',            $request->individual->contactInfo->email);
                $xmlOrder->addChild('firstname',        $request->individual->firstName);
                $xmlOrder->addChild('lastname',         $request->individual->lastName);
                $xmlOrder->addChild('phone_subscription',            $phone);
                $xmlOrder->addChild('phone',            $order->getphoneNotification());
                $xmlOrder->addChild('order_value',      $order->getGrandTotal());
                $xmlOrder->addChild('payment_method',   $paymentMethod);
                $xmlOrder->addChild('delivery_method',  $request->shipment->shipmentType);
                $xmlOrder->addChild('code',             $_code);
                $xmlOrder->addChild('status',           $_status);
                $xmlOrder->addChild('message',          $_message);

                $xmlOrder->addChild('product',          $_product);
                $xmlOrder->addChild('subscription',     $_subscription);
                $xmlOrder->addChild('activation_type',  $_activationType);
                $xmlOrder->addChild('sim_type',         $_simType);

                $xmlOrders->asXML($logFile);
               //Mage::log(print_r($xmlOrders->asXML(), 1));
            } else {
                $xmlOrder = array(
                    'time'             => $order->getCreatedAt(),
                    'orderid'          => $request->magentoOrderId,
                    'ssn'              => $request->individual->identificationNumber,
                    'email'            => $request->individual->contactInfo->email,
                    'firstname'        => $request->individual->firstName,
                    'lastname'         => $request->individual->lastName,
                    'phone_subscription'            => $phone,
                    'phone'            => $order->getphoneNotification(),
                    'order_value'      => $order->getGrandTotal(),
                    'payment_method'   => $paymentMethod,
                    'delivery_method'  => $request->shipment->shipmentType,
                    'code'             => $_code,
                    'status'           => $_status,
                    'message'          => $_message,
                );
                for($i = 1; $i < 3; $i++) {
                    if(isset($_product[$i]))
                        $xmlOrder['product_'.$i] = $_product[$i];
                    else
                        $xmlOrder['product_'.$i] = '';
                        
                    if(isset($_subscription[$i]))
                        $xmlOrder['subscription_'.$i] = $_subscription[$i];
                    else
                        $xmlOrder['subscription_'.$i] = '';
                        
                    if(isset($_activationType[$i]))
                        $xmlOrder['activation_type_'.$i] = $_activationType[$i];
                    else
                        $xmlOrder['activation_type_'.$i] = '';
                        
                    if(isset($_simType[$i]))
                        $xmlOrder['sim_type_'.$i] = $_simType[$i];
                    else
                        $xmlOrder['sim_type_'.$i] = '';
                }
                $logFilePoint = fopen($logFile, 'a');
                fputcsv($logFilePoint, $xmlOrder, ';');
            }
            
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
        //Mage::log($message, 1, 'logfile.log');
    }
    
    /**
     * getDataFromAdditionData
     * 
     * @param type $_quoteItem
     * @param type $field
     * @return $value or null
     */
    protected function getDataFromAdditionData($_quoteItem = null, $field = '')
    {
        if (is_null($_quoteItem) || empty($field)) {
            return null;
        }
        $_additionalData = $_quoteItem->getAdditionalData();
        if (!empty($_additionalData)) {
            $additionalData = unserialize($_additionalData);
            if (isset($additionalData[$field]) && !empty($additionalData[$field])) {
                $value = $additionalData[$field];
            } else {
                $value = null;
            }
            return $value;
        } else {
            return null;
        }
    }

  

    public function createOrderLog($orderId = null)
    {
        if (is_null($orderId)) {
            $orderIncrementId = Mage::getSingleton('checkout/type_onepage')->getLastOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        } else {
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        $xmlOrder = null;
        foreach ($order->getStatusHistoryCollection() as $historyItem) {
            if (preg_match('/^<\?xml/', $historyItem->getComment())) {
                $xmlOrder = simplexml_load_string($historyItem->getComment());
                break;
            }
        }
        if (!is_null($xmlOrder)) {
            $xmlOrder = $xmlOrder->xpath('/soapenv:Envelope/soapenv:Body/com:createOrder/*');
            $xmlOrder = array_shift($xmlOrder);
            //print_r($xmlOrder);
            $xml = $this->_baseLogXml();
            $body = $xml->Body;
            $logCreateOrder = $body->addChild('log:LogCreateOrder', null, 'http://comviq.se/logging');
            $logCreateOrderRequest = $logCreateOrder->addChild('log:createOrderRequest', null);
            
            foreach ($xmlOrder as $nodes) {
                $this->_sxmlAppend($logCreateOrderRequest, $nodes);
            }
            $logCreateOrder->addChild('sessionId', Mage::helper('tele4G_sS4Integration')->getCustomerSessionId());
            $logCreateOrder->addChild('clientIp', Mage::helper('tele4G_sS4Integration')->getCustomerIp());
            //echo $xml->asXML();
            Mage::log("request:\nwsdlUrl: {Mage::getStoreConfig('tele4G/ss4/wsdl_log')}\nxml:\n{$xml->asXML()}", 1, 'ss4.LogCreateOrder.log');
            $response = $this->sendRequest('log', $xml);
            Mage::log("response:\n{$response}", 1, 'ss4.LogCreateOrder.log');
            return $response;
        }
    }

    protected function _baseLogXml()
    {
        $xml = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:log="http://comviq.se/logging">
   <soapenv:Header/>
   <soapenv:Body>
   </soapenv:Body>
</soapenv:Envelope>
XML;
        $xmlElement = simplexml_load_string($xml, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");
        return $xmlElement;
    }

    protected function _sxmlAppend(SimpleXMLElement $to, SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
