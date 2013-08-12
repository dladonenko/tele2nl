<?php

/**
 * Private sales and stubs observer
 *
 */
class Tele2_WebsiteRestriction_Model_Mode_Session
{
    private $_client = null;
    
    public function getClient($wsdl)
    {
        if (!$this->_client) {
            $this->_client = @new SoapClient($wsdl,
                array('trace' => 1,
                    'exceptions' => 1,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'location' => $wsdl,
                    'soap_version' => 'SOAP_1_2',
                ));
        }

        return $this->_client;
    }

    public function getAuth($uid = '')
    {
        // Check referal url
        $referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $serviceReferalUrl = Mage::app()->getStore()->getConfig('general/restriction/request_service_referal_url');
        if (!($referrer && preg_match('@^http[s]?://('.$serviceReferalUrl.')@i', $referrer))) {
            return false;
        }

        $serviceWsdl = Mage::app()->getStore()->getConfig('general/restriction/request_service_wsdl');
        $serviceUsername = Mage::app()->getStore()->getConfig('general/restriction/request_service_username');
        $servicePassword = Mage::app()->getStore()->getConfig('general/restriction/request_service_password');
        
        $client = $this->getClient($serviceWsdl);
        if (!$client) {
            return false;
        }

        $result = new Varien_Object();
        $result->result = $client
            ->GetStudentDataFromUID($serviceUsername, $servicePassword, $uid);
        Mage::log(
            "requst params: \n".print_r(array($serviceUsername, $servicePassword, $uid), 1).
                "result as obj: \nGetStudentDataFromUID\n".print_r($result->result, 1)."\n",
            1,
            'mecenat.log'
        );
        if (
            strtolower($result->result->ReturnValue) === 'valid' &&
            $result->result->Id != '-1'
        ) {
            return true;
        }
        return false;
    }
}