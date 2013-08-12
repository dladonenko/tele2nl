<?php

/**
 * class aurigaWs
 * Class used to Administrate transactions in aurigas system via SOAP
 * @author Markus Gerdau <markus.gerdau@auriga.se>
 * @author Daniel Maison <daniel.maison@auriga.se>
 * @version 1.0
 * @copyright Copyright (c) 2009, Auriga AB
 *
 *==============================================================================
 
  Example use:
 
  $auriga = new aurigaWs();
  $transaction_id = "123456";
  $delivery_date = date("Ymd");
  $response = $auriga->confirm($transaction_id,$delivery_date,true);
 
  if ($response['Status'] == "A" && $response['Status_code'] == "0"){
    echo "Transaction confirmed";
 } else {
    echo "Something went wrong";
 }
 
 */
class aurigaWs {
    
    //Set merchant specific values
    protected $merchant_id = "XXXX";
    protected $secret ="aaaaaaabbbbbbbbbbcccccccccc";
    
    //Define the WSDL URL's
    public $testwsdl = "https://test-epayment.auriganet.eu/2009/09/23/admin/AdminService?WSDL";
    public $prodwsdl = "https://epayment.auriganet.eu/2009/09/23/admin/AdminService?WSDL";
    
    // Icommerce addon 
    public function __construct( $merchant_id, $secret ){
        $this->merchant_id = $merchant_id;
        $this->secret = $secret;
    }
    
    /*----------------------------------------------------------------------------------------------------------------------*/
    
    /**
     * function query
     * Function used to check status of a transaction in Aurigas system
     * @param string $customer_refno
     * @param boolean $test [OPTIONAL]
     * @return array $response
     */
    function query($customer_refno,$test = false){
    //Set the correct WSDL for test/prod             
        if($test == true){
            $client = new SoapClient($this->testwsdl);
    } else {
            $client = new SoapClient($this->prodwsdl);
    }
                        
    //Create the MAC     
    $mac = new macer();
    $mac->merchant_id = $this->merchant_id;
    $mac->secret = $this->secret;
    $mac->customer_refno = $customer_refno;
    $m = $mac->createMac();
        
    $params = array('merchantid' => $this->merchant_id,
                    'customerrefno' => $customer_refno,
                    'mac' => $m
                    );
        
    $rawResponse=(array)$client->query($params);
    $response = (array)$rawResponse['return'];
              
    //Clean the mac variables
    $mac->cleanMac();
        
    //Create MAC for the response from Auriga
    $mac->secret = $this->secret;
    $mac->status = $response['Status'];
    $mac->status_code = $response['StatusCode'];
    $mac->customer_refno = $response['Customerrefno'];
    $mac->comment = $response['Comment'];
    $mac->transaction_id = $response['TransactionId'];
    $mac->auth_code= $response['AuthCode'];
    $mac->payment_method = $response['PaymentMethod'];
    $mac->card_type= $response['CardType'];
    $mac->authorized_amount= $response['AuthorizedAmount'];
    $mac->credit_amount = $response['CreditAmount'];
    $mac->fee_amount= $response['FeeAmount'];
    $mac->risk_score = $response['RiskScore'];
    $mac->threed_sec= $response['ThreeDSecure'];
    $mac->batch_id = $response['BatchId'];
    $mac->issuing_bank = $response['IssuingBank'];
    $mac->ip_country = $response['IpCountry'];
    $mac->issuing_country = $response['IssuingCountry'];
    $mac->card_num = $response['CardNum'];
    $mac->currency = $response['Currency'];
    $mac->exp_date = $response['ExpDate'];
    $mac->credit_status_code = $response['CreditStatusCode'];
                
    //Compare the recieved MAC to the calculated one
    $mac2 = $mac->createMac();
    if(!$mac->compare($mac2,$response['Mac'])){
    return "Could not validate response";
    }
    return $response;
    }


/*----------------------------------------------------------------------------------------------------------------------*/

    /**
     * function annul
     * Function used to anull a transaction in Aurigas system
     * @param string $transaction_id
     * @param boolean $test [OPTIONAL]
     * @return array $response
     */
    function annul($transaction_id,$test=false){
        //Set the correct WSDL for test/prod     
        if($test == true){
            $client = new SoapClient($this->testwsdl);
        } else {
            $client = new SoapClient($this->prodwsdl);
        }
        //Create the MAC             
        $mac = new macer();
        $mac->merchant_id = $this->merchant_id;
        $mac->secret = $this->secret;
        $mac->transaction_id = $transaction_id;
        $m = $mac->createMac();
        
        $params = array('merchantid' => $this->merchant_id,
                        'transactionid' => $transaction_id,
                        'mac' => $m
                        );
    
        $rawResponse=(array)$client->annul($params);
        $response= (array)$rawResponse['return'];
        
        //Clean the mac variables
        $mac->cleanMac();
        
        //Create MAC for the response from Auriga
        $mac->secret = $this->secret;
        $mac->status = $response['Status'];
        $mac->status_code = $response['StatusCode'];
        $mac->customer_refno = $response['Customerrefno'];
        $mac->comment = $response['Comment'];
        $mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
            return "Could not validate response";
        }        
            return $response;
    }

/*----------------------------------------------------------------------------------------------------------------------*/

    /**
     * function authrev
     * Function used to lower the authorized amount of a transaction in Aurigas system
     * @param string $transaction_id
     * @param string $amount
     * @param string $vat
     * @param boolean $test [OPTIONAL]
     * @return array $response
     */
    function authrev($transaction_id,$amount,$vat,$test=false){
        if($test == true){
            $client = new SoapClient($this->testwsdl);
        } else {
            $client = new SoapClient($this->prodwsdl);
        }
            
    $mac = new macer();
    $mac->merchant_id = $this->merchant_id;
    $mac->secret = $this->secret;
    $mac->transaction_id = $transaction_id;
    $mac->amount = $amount;
    $mac->vat = $vat;
    $m = $mac->createMac();
    
    $params = array('merchantid' => $this->merchant_id,
            'transactionid' => $transaction_id,
            'amount' => $amount,
            'vat' => $vat,
            'mac' => $m
                        );
    
    $rawResponse=(array)$client->authRev($params);
    $response = (array)$rawResponse['return'];

        //Clean the mac variables
    $mac->cleanMac();
        
        //Create MAC for the response from Auriga
        $mac->secret = $this->secret;
        $mac->status = $response['Status'];
        $mac->status_code = $response['StatusCode'];
        $mac->customer_refno = $response['Customerrefno'];
        $mac->comment = $response['Comment'];
        $mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
            return "Could not validate response";
        }
        return $response;
    }
                 
/*----------------------------------------------------------------------------------------------------------------------*/

    /**
    * function confirm
    * Function used to confirm an authorized transaction in Aurigas system
    * @param string $transaction_id
    * @param string $delivery_date
    * @param boolean $test [OPTIONAL]
    * @return array $response
    */                 
             
    function confirm($transaction_id,$delivery_date,$test=false){
		if($test == true){
				$client = new SoapClient($this->testwsdl);
		} else {
				$client = new SoapClient($this->prodwsdl);
		}
				
		$mac = new macer();
		$mac->merchant_id = $this->merchant_id;
		$mac->secret = $this->secret;
		$mac->transaction_id = $transaction_id;
		$mac->delivery_date = $delivery_date;
		$m = $mac->createMac();
			
		$params = array('merchantid' => $this->merchant_id,
				'transactionid' => $transaction_id,
				'deliverydate' => $delivery_date,
				'mac' => $m
							);
		
		$rawResponse=(array)$client->confirm($params);
		$response = (array)$rawResponse['return'];
						   
			//Clean the mac variables
		$mac->cleanMac();
		
			//Create MAC for the response from Auriga
		$mac->secret = $this->secret;
		$mac->status = $response['Status'];
		$mac->status_code = $response['StatusCode'];
		$mac->customer_refno = $response['Customerrefno'];
		$mac->comment = $response['Comment'];
		$mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
			return "Could not validate response";
        }   
		return $response;
    }
                 
                 
/*----------------------------------------------------------------------------------------------------------------------*/

    /**
    * function credit
    * Function used to credit a settled transaction in Aurigas system
    * @param string $transaction_id
    * @param string $amount
    * @param string $vat
    * @param boolean $test [OPTIONAL]
    * @return array $response
    */   
    function credit($transaction_id,$amount,$vat,$test=false){
     if($test == true){
            $client = new SoapClient($this->testwsdl);
    } else {
            $client = new SoapClient($this->prodwsdl);
    }
            
        $mac = new macer();
        $mac->merchant_id = $this->merchant_id;
        $mac->secret = $this->secret;
        $mac->transaction_id = $transaction_id;
        $mac->amount = $amount;
        $mac->vat = $vat;
        $m = $mac->createMac();
                
        $params = array('merchantid' => $this->merchant_id,
            'transactionid' => $transaction_id,
            'amount' => $amount,
            'vat' => $vat,
            'mac' => $m
                                );
    
    $rawResponse=(array)$client->credit($params);
    $response = (array)$rawResponse['return'];
                        
        //Clean the mac variables
        $mac->cleanMac();
        
        //Create MAC for the response from Auriga
        $mac->secret = $this->secret;
        $mac->status = $response['Status'];
        $mac->status_code = $response['StatusCode'];
        $mac->customer_refno = $response['Customerrefno'];
        $mac->comment = $response['Comment'];
        $mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
                return "Could not validate response";
        }                           
        return $response;
    }

/*----------------------------------------------------------------------------------------------------------------------*/

    /**
     * function creditAnnul
     * Function used to annul a credit that is not yet settled in Aurigas system
     * @param string $transaction_id
     * @param boolean $test [OPTIONAL]
     * @return array $response
     */   

    function creditAnnul($transaction_id,$test=false){
    if($test == true){
            $client = new SoapClient($this->testwsdl);
    } else {
            $client = new SoapClient($this->prodwsdl);
    }
            
    $mac = new macer();
    $mac->merchant_id = $this->merchant_id;
    $mac->secret = $this->secret;
    $mac->transaction_id = $transaction_id;
    $m = $mac->createMac();
        
        $params = array('merchantid' => $this->merchant_id,
            'transactionid' => $transaction_id,
            'mac' => $m
                        );
    
    $rawResponse=(array)$client->creditAnnul($params);
    $response= (array)$rawResponse['return'];
        //Clean the mac variables
    $mac->cleanMac();
        
        //Create MAC for the response from Auriga
    $mac->secret = $this->secret;
    $mac->status = $response['Status'];
    $mac->status_code = $response['StatusCode'];
    $mac->customer_refno = $response['Customerrefno'];
    $mac->comment = $response['Comment'];
    $mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
            return "Could not validate response";
    }                          
    return $response;
    }

/*----------------------------------------------------------------------------------------------------------------------*/

    /**
    * function recurPay
    * Function used to request a recurring payment
    * @param string $subscription_id
    * @param string $customerrefno
    * @param string $amount
    * @param string $vat
    * @param boolean $test [OPTIONAL]
    * @return array $response
    */   

    function recurPay($subscription_id,$customerrefno,$amount,$vat,$test=false){
        if($test == true){
                $client = new SoapClient($this->testwsdl);
        } else {
                $client = new SoapClient($this->prodwsdl);
        }
            
        $mac = new macer();
        $mac->merchant_id = $this->merchant_id;
        $mac->secret = $this->secret;
        $mac->subscription_id = $subscription_id;
        $mac->customer_refno = $customerrefno;
        $mac->amount = $amount;
        $mac->vat = $vat;
        $m = $mac->createMac();
    
        $params = array('merchantid' => $this->merchant_id,
            'subscriptionid' => $subscription_id,
            'customerrefno' => $customerrefno,
            'amount' => $amount,
            'vat' => $vat,
            'mac' => $m
                        );
    
        $rawResponse=(array)$client->recurPay($params);
        $response = (array)$rawResponse['return'];
        
        //Clean the mac variables
        $mac->cleanMac();
        
        //Create MAC for the response from Auriga
        $mac->secret = $this->secret;
        $mac->status = $response['Status'];
        $mac->status_code = $response['StatusCode'];
        $mac->customer_refno = $response['Customerrefno'];
        $mac->comment = $response['Comment'];
        $mac->transaction_id = $response['TransactionId'];
                       
        //Compare the recieved MAC to the calculated one
        $mac2 = $mac->createMac();
        if(!$mac->compare($mac2,$response['Mac'])){
            return "Could not validate response";
        }                                                  
            return $response;
    }

/*----------------------------------------------------------------------------------------------------------------------*/

    /**
    * function ping
    * Function used to check if Aurigas system is online or not
    * @param boolean $test [OPTIONAL]
    * @return array $response
    */   

    function ping($test=false){
        if($test == true){
                $client = new SoapClient($this->testwsdl);
        } else {
                $client = new SoapClient($this->prodwsdl);
        }
        $rawResponse=(array)$client->ping();
        $response = (array)$rawResponse['return'];
        return $response;
    }

}


/*----------------------------------------------------------------------------------------------------------------------*/


/**
 * class macer
 * Class used to calculate MAC with Auriga ePayments parameters
 * @author Markus Gerdau <markus.gerdau@auriga.se>
 * @author Daniel Maison <daniel.maison@auriga.se>
 * @copyright Copyright (c) 2009, Auriga AB
 */ 
class macer{
        //Declare all variables
        public $merchant_id;
        public $version;
        public $customer_refno;
        public $subscription_refno;
        public $transaction_id;
        public $torget_orderno;
        public $status;
        public $status_code;
    public $credit_status_code;
        public $auth_code;
        public $threed_sec;
        public $batch_id;
        public $currency;
        public $amount;
        public $shipping_cost;
        public $vat;
        public $payment_method;
        public $purchase_date;
        public $response_url;
        public $orig_response_url;
        public $request_type;
        public $transaction_type;
        public $delivery_date;
        public $transaction_date;
        public $capture_id;
        public $credit_id;
        public $goods_description;
        public $language;
        public $capture_now;
        public $comment;
        public $to_accnt;
        public $from_accnt;
        public $payson_guarantee;
        public $cert;
        public $consumer_name;
        public $email_address;
        public $card_num;
        public $cvx2;
        public $exp_date;
        public $card_type;
        public $risk_score;
    public $issuing_bank;
    public $ip_country;
    public $issuing_country;
        public $phone_num;
        public $card_specific;
        public $track2;
        public $country;
        public $cancel_url;
        public $exclude_method;
        public $exclude_card;
        public $ocr_number;
        public $personal_identity;
        public $last_dayofpayment;
        public $name;
        public $street_address;
        public $complementary_address;
        public $city_address;
        public $postalcode_address;
        public $net_secure_id;
        public $bank_code;
        public $authorized_amount;
        public $fee_amount;
    public $credit_amount;
        public $card_fee;
        public $auth_null;
        public $moto_method;
        public $sub_method;
        public $submethod_status;
        public $prepaid_card_num;
        public $prepaid_load_amount;
        public $prepaid_activation_code;
        public $prepaid_balance;
        public $prepaid_expirydate;
        public $prepaid_cardstatus;
        public $prepaid_resultvalue;
        public $transaction_list_amount;
        public $transaction_list_currency;
        public $transaction_list_date;
        public $transaction_list_posid;
        public $transaction_list_store;
        public $transaction_list_time;
        public $transaction_list_transactionid;
        public $transaction_list_transactionresult;
        public $transaction_list_transactiontype;
    public $batch_list_auriga_batchid;
    public $batch_list_batchid;
    public $batch_list_cardbrand;
    public $batch_list_currency;
    public $batch_list_date;
    public $batch_list_amount;
    public $batch_list_amountcredit;
    public $transaction_item_id;
    public $transaction_item_ordernumber;
    public $transaction_item_purchasedate;
    public $transaction_item_captureamount;
    public $transaction_item_currency;
    public $transaction_item_capturedate;
    public $transaction_item_type;
    public $consent;    
        public $secret;

    /**
     * function createMac
     * Function used to calculate the MAC (Message authentication code)
     * @return string $mac
     */         
    function createMac(){
        
        if (!$this->secret || strlen($this->secret) != "32"){
            return "Wrong/missing secret word";
        } else{
            $macinput ="";
            $macinput .=$this->merchant_id;
            $macinput .=$this->version;
            $macinput .=$this->customer_refno;
            $macinput .=$this->subscription_refno;
            $macinput .=$this->transaction_id;
            $macinput .=$this->torget_orderno;
            $macinput .=$this->status;
            $macinput .=$this->status_code;
        $macinput .=$this->credit_status_code;
        $macinput .=$this->auth_code;
            $macinput .=$this->threed_sec;
            $macinput .=$this->batch_id;
            $macinput .=$this->currency;
            $macinput .=$this->amount;
            $macinput .=$this->shipping_cost;
            $macinput .=$this->vat;
            $macinput .=$this->payment_method;
            $macinput .=$this->purchase_date;
            $macinput .=$this->response_url;
            $macinput .=$this->orig_response_url;
            $macinput .=$this->request_type;
            $macinput .=$this->transaction_type;
            $macinput .=$this->delivery_date;
            $macinput .=$this->transaction_date;
            $macinput .=$this->capture_id;
            $macinput .=$this->credit_id;
            $macinput .=$this->goods_description;
            $macinput .=$this->language;
            $macinput .=$this->capture_now;
            $macinput .=$this->comment;
            $macinput .=$this->to_accnt;
            $macinput .=$this->from_accnt;
            $macinput .=$this->payson_guarantee;
            $macinput .=$this->cert;
            $macinput .=$this->consumer_name;
            $macinput .=$this->email_address;
            $macinput .=$this->card_num;
            $macinput .=$this->cvx2;
            $macinput .=$this->exp_date;
            $macinput .=$this->card_type;
            $macinput .=$this->risk_score;
            $macinput .=$this->issuing_bank;
        $macinput .=$this->ip_country;  
            $macinput .=$this->issuing_country;                  
            $macinput .=$this->phone_num;
            $macinput .=$this->card_specific;
            $macinput .=$this->track2;
            $macinput .=$this->country;
            $macinput .=$this->cancel_url;
            $macinput .=$this->exclude_method;
            $macinput .=$this->exclude_card;
            $macinput .=$this->ocr_number;
            $macinput .=$this->personal_identity;
            $macinput .=$this->last_dayofpayment;
            $macinput .=$this->name;
            $macinput .=$this->street_address;
            $macinput .=$this->complementary_address;
            $macinput .=$this->city_address;
            $macinput .=$this->postalcode_address;
            $macinput .=$this->net_secure_id;
            $macinput .=$this->bank_code;
            $macinput .=$this->authorized_amount;
            $macinput .=$this->fee_amount;
            $macinput .=$this->credit_amount;        
            $macinput .=$this->card_fee;
            $macinput .=$this->auth_null;
            $macinput .=$this->moto_method;
            $macinput .=$this->sub_method;
            $macinput .=$this->submethod_status;
            $macinput .=$this->prepaid_card_num;
            $macinput .=$this->prepaid_load_amount;
            $macinput .=$this->prepaid_activation_code;
            $macinput .=$this->prepaid_balance;
            $macinput .=$this->prepaid_expirydate;
            $macinput .=$this->prepaid_cardstatus;
            $macinput .=$this->prepaid_resultvalue;
            $macinput .=$this->transaction_list_amount;
            $macinput .=$this->transaction_list_currency;
            $macinput .=$this->transaction_list_date;
            $macinput .=$this->transaction_list_posid;
            $macinput .=$this->transaction_list_store;
            $macinput .=$this->transaction_list_time;
            $macinput .=$this->transaction_list_transactionid;
            $macinput .=$this->transaction_list_transactionresult;
            $macinput .=$this->transaction_list_transactiontype;
        $macinput .=$this->batch_list_auriga_batchid;
        $macinput .=$this->batch_list_batchid;
        $macinput .=$this->batch_list_cardbrand;
        $macinput .=$this->batch_list_currency;
        $macinput .=$this->batch_list_date;
        $macinput .=$this->batch_list_amount;
        $macinput .=$this->batch_list_amountcredit;
        $macinput .=$this->transaction_item_id;
        $macinput .=$this->transaction_item_ordernumber;
        $macinput .=$this->transaction_item_purchasedate;
        $macinput .=$this->transaction_item_captureamount;
        $macinput .=$this->transaction_item_currency;
        $macinput .=$this->transaction_item_capturedate;
        $macinput .=$this->transaction_item_type;
        $macinput .=$this->consent;            
            $macinput .=$this->secret;
                $mac=md5($macinput);
                return $mac;
        }
    
    }
        /*----------------------------------------------------------------------------------------------------------------------*/
        /**
     * function cleanMac
     * Function used to set all parameters to null
     * @return boolean
     */   
    function cleanMac(){
        
            $this->merchant_id="";
            $this->version="";
            $this->customer_refno="";
            $this->subscription_refno="";
            $this->transaction_id="";
            $this->torget_orderno="";
            $this->status="";
            $this->status_code="";
            $this->credit_status_code="";        
            $this->auth_code="";
            $this->threed_sec="";
            $this->batch_id="";
            $this->currency="";
            $this->amount="";
            $this->shipping_cost="";
            $this->vat="";
            $this->payment_method="";
            $this->purchase_date="";
            $this->response_url="";
            $this->orig_response_url="";
            $this->request_type="";
            $this->transaction_type="";
            $this->delivery_date="";
            $this->transaction_date="";
            $this->capture_id="";
            $this->credit_id="";
            $this->goods_description="";
            $this->language="";
            $this->capture_now="";
            $this->comment="";
            $this->to_accnt="";
            $this->from_accnt="";
            $this->payson_guarantee="";
            $this->cert="";
            $this->consumer_name="";
            $this->email_address="";
            $this->card_num="";
            $this->cvx2="";
            $this->exp_date="";
            $this->card_type="";
            $this->risk_score="";
            $this->issuing_bank="";
            $this->ip_country="";
            $this->issuing_country="";        
            $this->phone_num="";
            $this->card_specific="";
            $this->track2="";
            $this->country="";
            $this->cancel_url="";
            $this->exclude_method="";
            $this->exclude_card="";
            $this->ocr_number="";
            $this->personal_identity="";
            $this->last_dayofpayment="";
            $this->name="";
            $this->street_address="";
            $this->complementary_address="";
            $this->city_address="";
            $this->postalcode_address="";
            $this->net_secure_id="";
            $this->bank_code="";
            $this->authorized_amount="";
            $this->fee_amount="";
            $this->credit_amount="";        
            $this->card_fee="";
            $this->auth_null="";
            $this->moto_method="";
            $this->sub_method="";
            $this->submethod_status="";
            $this->prepaid_card_num="";
            $this->prepaid_load_amount="";
            $this->prepaid_activation_code="";
            $this->prepaid_balance="";
            $this->prepaid_expirydate="";
            $this->prepaid_cardstatus="";
            $this->prepaid_resultvalue="";
            $this->transaction_list_amount="";
            $this->transaction_list_currency="";
            $this->transaction_list_date="";
            $this->transaction_list_posid="";
            $this->transaction_list_store="";
            $this->transaction_list_time="";
            $this->transaction_list_transactionid="";
            $this->transaction_list_transactionresult="";
            $this->transaction_list_transactiontype="";
            $this->batch_list_auriga_batchid="";
            $this->batch_list_batchid="";
            $this->batch_list_cardbrand="";
            $this->batch_list_currency="";
            $this->batch_list_date="";
            $this->batch_list_amount="";
            $this->batch_list_amountcredit="";
            $this->transaction_item_id="";
            $this->transaction_item_ordernumber="";
            $this->transaction_item_purchasedate="";
            $this->transaction_item_captureamount="";
            $this->transaction_item_currency="";
            $this->transaction_item_capturedate="";
            $this->transaction_item_type="";
            $this->consent="";            
            $this->secret="";
                    
            return true;
    }

/*----------------------------------------------------------------------------------------------------------------------*/
        /**
     * function compare
     * Function used to compare two MAC strings
     * @param string $mac
     * @param string $mac1
     * @return boolean
     */         
    function compare($mac,$mac1){
        
        if($mac == $mac1){
                return true;
        } else {
                return false;
        }
    }
    
}
?>