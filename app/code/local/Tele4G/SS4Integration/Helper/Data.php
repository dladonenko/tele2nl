<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_SS4Integration
 */
class Tele4G_SS4Integration_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getClient($wsdlType = null)
    {
        if ($wsdlType == 'togo') {
            $wsdlUrl = Mage::getStoreConfig('carriers/togo/wsdl');
        } elseif ($wsdlType == 'log') {
            $wsdlUrl = Mage::getStoreConfig('tele4G/ss4/wsdl_log');
        } else {
            $wsdlUrl = Mage::getStoreConfig('tele4G/ss4/wsdl');
        }

        $client = @new SoapClient($wsdlUrl,
                array('trace' => 1,
                    'exceptions' => 1,
                    'cache_wsdl'=>WSDL_CACHE_MEMORY,
                    'location'=>$wsdlUrl,
                    'soap_version' => 'SOAP_1_2',
                ));

        return $client;
    }
    
    
    public function getSs4Result($method, $params, $logfile = 'result', $wsdlType = 'ss4')
    {
        $response = new stdClass();

        if ($method == 'getIndividual') {
            $response = $this->_getIndividual($params);
        } else if ($method == 'getActivationTypeForExistingNumber') {
            $activationType = $this->_getActivationTypeByNumber((string)$params->activationTypeRequest['mobilePhoneNumber']['number']);
            $response = new stdClass();
            $response->activationType =  (object)array(
                'responseStatus' => (object)array('status' => 'OK', 'errorCode'=> 0 ),
                'activationType' => $activationType
            );
        } /*else {
            $oldSocketTimeOut = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 10);

            $startTime = time();

            $debugArray = array('METHOD' => $method);
            try {
                $client = $this->getClient($wsdlType);
                $ssnRequest = $client->$method($params);
                $response = $ssnRequest;
                $debugArray['SERVICE_URL'] = $client->location;
                $debugArray['REQUEST']     = $client->__getLastRequest();
                $debugArray['RESPONSE']    = $client->__getLastResponse();
            } catch (Exception $e) {
                Mage::logException($e);
                $response = $e->getMessage();
                $debugArray['ERROR'] = $e->getMessage();
            }
            $debugArray['TIME'] = time() - $startTime;
            Mage::log(print_r($debugArray, true), null, 'ss4.'.$logfile.'.log');

            ini_set('default_socket_timeout', $oldSocketTimeOut);
        }*/

        return $response;
    }

    //result die()

    /**
     * Returns decrypted cookie value
     * @todo: move key to config
     *
     * @param $cookie - cookie with encrypted value
     */
    public function decryptCookie($cookie)
    {
        $key = 'Spongebob42--Spongebob42';

        try{
            $cookie = str_replace(' ', '+', $cookie);
            $crypt = base64_decode($cookie);
            $td = mcrypt_module_open (MCRYPT_TRIPLEDES, 'var/cache', MCRYPT_MODE_ECB, 'var/cache');
            $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            mcrypt_generic_init($td, $key, $iv);
            $decrypted_data = mdecrypt_generic ($td, $crypt);
            mcrypt_generic_deinit ($td);
            mcrypt_module_close ($td);
            $decrypted_data = rtrim($decrypted_data);
            Mage::log($cookie."\n".print_r($decrypted_data, true), null, 'ss4.cookie.log');
            return $decrypted_data;
        } catch (Exception $e) {
            $debugArray = array(
                'ERROR'=>$e->getMessage(),
                'ENCRYPTED'=>$cookie,
            );
            Mage::log(print_r($debugArray, true), null, 'ss4.cookie.log');
        }
        return null;
    }

    public function getInfoBySsn($ssn = null)
    {
        if ($ssn) {
            $params = new stdClass();
            $params->identificationNumber = $ssn;
            
            $ssnRequest = $this->getSs4Result(
                'getIndividual',
                $params,
                'infobyssn'
            );
            return $ssnRequest;        
        }
        return false;
    }

    public function getValidateOkToProlong($ssn = null, $number = null)
    {
        if ($ssn && $number) {
            $params = new stdClass();
            $params->validateOkToProlongRequest = array(
                'identificationNumber' => $ssn,
                'mobilePhoneNumber' => $number,
            );
            $validateOkToProlong = $this->getSs4Result(
                'validateOkToProlong',
                $params,
                'validateOkToProlong'
            );
            return $validateOkToProlong->validateOkToProlongResponse->responseStatus;
        }
        return false;
    }
  
    public function ssnInfoLog($ssn = null)
    {
        if ($ssn) {
            Mage::getModel('tele4G_sS4Integration/sS4Integration')->ssnInfoLog($ssn);
        };
    }
    
    public function createOrderLog($orderId = null)
    {
        Mage::getModel('tele4G_sS4Integration/sS4Integration')->createOrderLog($orderId);
    }


    /**
     * Validate Number for Purchant Assistant
     */
    public function validateNumber($number, $code)
    {
        $params = new stdClass();
        $params->sendChallengeSmsRequest = array('mobilePhoneNumber'=>array('number'=>$number), 'billPlanCode'=>$code);
        $ssnRequest = $this->getSs4Result(
            'getActivationTypeForExistingNumber',
            $params,
            'sendchallengesms'
        );        
        $result = new Varien_Object();
        $result->result = $ssnRequest->sendChallengeSmsResponse;
        return $result;
    }

    public function sendChallengeSms($number)
    {
        $params = new stdClass();
        $params->sendChallengeSmsRequest = array('phoneNumber'=>array('number'=>$number));
        $ssnRequest = $this->getSs4Result(
            'sendChallengeSms',
            $params,
            'sendchallengesms'
        );        
        $result = new Varien_Object();
        $result->result = $ssnRequest->sendChallengeSmsResponse;
        return $result;
    }

    public function validateSmsCode($number, $code)
    {
        $params = new stdClass();
        $params->validateSmsCodeRequest = array('phoneNumber'=>array('number'=>$number), 'code'=>$code);
        $ssnRequest = $this->getSs4Result(
            'validateSmsCode',
            $params,
            'validatesmscode'
        );        
        $result = new Varien_Object();
        $result->result = $ssnRequest->validateSmsCodeResponse;
        return $result;
    }

    /**
     * Returns activation type for existing number
     * @param $number
     * @param $code
     * @return Varien_Object
     */
    public function getATypeForExistNum($number, $code)
    {
        $params = new stdClass();
        $params->activationTypeRequest = array('mobilePhoneNumber'=>array('number'=>$number), 'billPlanCode'=>$code);
        $ssnRequest = $this->getSs4Result(
            'getActivationTypeForExistingNumber',
            $params,
            'activationtype'
        );        
        $result = new Varien_Object();
        $result->result = $ssnRequest->activationType;

        return $result;
    }

    public function getAvailablePhoneNumbers($quantity = null)
    {
        $availablePhoneNumbersCache = Mage::getSingleton('checkout/session')->getAvailablePhoneNumbers();
        if (!empty($availablePhoneNumbersCache)) {
            $availablePhoneNumbers = unserialize($availablePhoneNumbersCache);
            if (!count($availablePhoneNumbers)){
                Mage::getSingleton('checkout/session')->unsAvailablePhoneNumbers();
            } else {
                return $availablePhoneNumbers;
            }
        }
        
        /*
        $params = new stdClass();
        $params->availablePhoneNumbersRequest = array('quantity'=>$quantity);
        
        $ssnRequest = $this->getSs4Result(
            'getAvailablePhoneNumbers',
            $params,
            'numbers'
        ); 
        
        if (!isset($ssnRequest->phoneNumbers->phoneNumber)) {
            return false;
        }

        foreach ($ssnRequest->phoneNumbers->phoneNumber as $number) {
            $numbers[] = $number->number;
        }
        */
        
        
        //New phone numbers are mocked 
        $numbers = array('0704199226','0704199355','0704199522','0704203002','0704203446');
        
        Mage::getSingleton('checkout/session')->setAvailablePhoneNumbers(serialize($numbers));
        return $numbers;
    }

    public function removeChosenNumber($numberToRemove)
    {
        $availablePhoneNumbersCache = Mage::getSingleton('checkout/session')->getAvailablePhoneNumbers();
        if (empty($availablePhoneNumbersCache)) {
            return false;
        }

        $availablePhoneNumbers = unserialize($availablePhoneNumbersCache);
        if (!count($availablePhoneNumbers)){
            return false;
        }

        foreach ($availablePhoneNumbers as $key=>$number) {
            if ($numberToRemove == $number) {
                unset($availablePhoneNumbers[$key]);
                Mage::getSingleton('checkout/session')->setAvailablePhoneNumbers(serialize($availablePhoneNumbers));
                return true;
            }
        }
    }

    public function getStockLevel($articleId)
    {
        $params = new stdClass();
        //$params->stockLevelsRequest = array('logisticsArticleIds'=>array('logisticsArticleId'=>$articleId), 'resellerId'=>'?');
        $params->stockLevelsRequest = array('logisticsArticleIds'=>array('logisticsArticleId'=>$articleId));
        $ssnRequest = $this->getSs4Result(
            'getStockLevel',
            $params,
            'updatestock'
        );
        return $ssnRequest;
    }
    
    /**
     * @param null $error Error code from SS4
     * @return bool|string Error message on the site
     */
    public function getErrorFromXML($error = NULL){
        if (!$error) {
            return false;
        }

        $errorListFile = Mage::getBaseDir() . DS . 'docs/ErrorCodeList.xml';
        $xml = simplexml_load_file($errorListFile);
        if (($xml->{$error}) && ($xml->{$error} != '')){
            $errorMsg = (string)$xml->{$error};
        } else {
            $errorMsg = $error;
        }
        return $this->__($errorMsg);
    }

    /** Retrive has Customer OrderInProgress by SSN
     * 
     * @return \Varien_Object
     */
    public function hasOrderInProgress($ssn = '')
    {
        if (!$ssn) {
            return false;
        }
        $params = new stdClass();
        $params->orderInProgressRequest = array('identificationNumber' => $ssn);
        $checkOrderRequest = $this->getSs4Result(
            'hasOrderInProgress',
            $params,
            'OrderInProgress',
            'togo'
        );        
        $result = new Varien_Object();
        $result->result = $checkOrderRequest->orderInProgressResponse;
        return $result;
    }

    public function searchOrdersForPos($ssn = '')
    {
        if (!$ssn) {
            return false;
        }
        $params = new stdClass();
        $params->ordersForPosRequest = array('identificationNumber' => $ssn);
        $checkOrderRequest = $this->getSs4Result(
            'searchOrdersForPos',
            $params,
            'searchOrdersAndCancel',
            'togo'
        );
        if ($checkOrderRequest->ordersForPosResponse->responseStatus->errorCode == '0') {
            foreach ($checkOrderRequest->ordersForPosResponse->orderDetails->orderDetail as $orderDetail) {
                if (strtolower($orderDetail->orderStatus) == 'pending') {
                    $cancelOrderId = $orderDetail->orderId;
                    return $this->cancelOrder($cancelOrderId);
                }
            }
        }
    }

    /** Cancel Customer Order by SSN
     * 
     * @return \Varien_Object
     */
    public function cancelOrder($orderId = '')
    {
        if (!$orderId) {
            return false;
        }
        $params = new stdClass();
        $params->orderId = $orderId;
        $cancelOrderRequest = $this->getSs4Result(
            'cancelOrder',
            $params,
            'searchOrdersAndCancel',
            'togo'
        );        
        $result = new Varien_Object();
        $result->result = $cancelOrderRequest->cancelOrderResponse;
        return $result;
    }

    /** Retrive Resseler Cities
     * 
     * @return \Varien_Object
     */
    public function getResellerCities()
    {   
        $params = new stdClass();
        $params->getResellerCities = array();
        $cityRequest = $this->getSs4Result(
            'getResellerCities',
            $params,
            'ResellerCities',
            'togo'
        );
        $result = null;
        if (isset($cityRequest->resellerCitiesResponse->cities->city)) {
            $result = $cityRequest->resellerCitiesResponse->cities->city;
        }
        return $result;
    }

    /** Retrive Resellers For Article And City
     * 
     * @return \Varien_Object
     */
    public function getResellersForArticleAndCity($requestParams = array())
    {
        $result = new Varien_Object();
        if (!isset($requestParams['city']) || !isset($requestParams['article_id'])) {
            $result->errorMsg = 'No data for request';
            Mage::log('getResellersForArticleAndCity: '.print_r($result->errorMsg, true), null, 'ss4.ResellerCities.log');
        } else {
            $params = new stdClass();
            $params->resellerInformationRequest = array(
                'city' => $requestParams['city'],
                'logisticArticleId' => $requestParams['article_id']
            );
            $cityRequest = $this->getSs4Result(
                'getResellersForArticleAndCity',
                $params,
                'ResellerCities',
                'togo'
            );
            if (isset($cityRequest->getResellersForArticleAndCityResponse)) {
                $result->result = $cityRequest->getResellersForArticleAndCityResponse;
            }
        }
        return $result;
    }
    
    /** Retrive Resellers City For Article
     * 
     * @return \Varien_Object
     */
    public function getResellersCityForArticle($requestParams = array())
    {
            $result = new Varien_Object();
        if (!isset($requestParams['article_id'])) {
            $result->errorMsg = 'No data for request';
        } else {
            $params = new stdClass();
            $params->getResellersCityForArticle = array(
                'logisticArticleId' => $requestParams['article_id']
            );
            $cityRequest = $this->getSs4Result(
                'getResellersCityForArticle',
                $params,
                'ResellerCities',
                'togo'
            );        
            $result->result = $cityRequest->getResellersCityForArticle;
        }
        return $result;
    }

    /**
     * Retrive Customer's session Id
     * 
     * @return string SessionId
     */
    public function getCustomerSessionId()
    {
        return Mage::getSingleton('log/visitor')->getSessionId();
    }

    /**
     * Retrive Customer's Ip
     * 
     * @return string
     */
    public function getCustomerIp()
    {
        $customerSessionData = Mage::getModel('customer/session')->getData('_session_validator_data');
        return $customerSessionData['remote_addr'];
    }

    /**
     * Mock individual response from ss4 server
     * @param array $params
     * @return stdClass
     */
    protected function _getIndividual($params)
    {
        $response = new stdClass();

        $response->individualResponse = new stdClass();
        $response->individualResponse->responseStatus = new stdClass();
        $response->individualResponse->responseStatus->status = "OK";
        $response->individualResponse->responseStatus->errorCode = 0;

        $response->individualResponse->individual = new stdClass();
        $response->individualResponse->individual->contactInfo = new StdClass();
        $response->individualResponse->individual->identificationNumber = $params->identificationNumber;

        $response->individualResponse->individual->firstName = 'Sören Anders';
        $response->individualResponse->individual->lastName = 'Knutsson';

        $response->individualResponse->individual->address = new stdClass();
        $response->individualResponse->individual->address->streetAddress = 'PL 11155 TEGELBRUKSVILLAN';
        $response->individualResponse->individual->address->city = 'ÖREBRO';
        $response->individualResponse->individual->address->postalCode = '70233';
        return $response;
    }

    /**
     * @param $number
     * @return string
     */
    protected function _getActivationTypeByNumber($number)
    {
        $firstNumber = substr($number, 0, 1);

        switch ($firstNumber) {
            case '0':
                $activationType = 'PORT';
                break;
            case '1':
                $activationType = 'CONVERT';
                break;
            case '2':
                $activationType = 'PROLONG';
                break;
            default:
                $activationType = 'NONE';
                break;
        }
        return $activationType;
    }
}
