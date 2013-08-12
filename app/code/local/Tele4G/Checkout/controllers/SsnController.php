<?php
/**
 * Tele4G_Checkout_SsnController
 */
class Tele4G_Checkout_SsnController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $body = null;
        $this->params = $this->getRequest()->getParams();
        $response = new Varien_Object();
        try {
            if ($this->getRequest()->isXmlHttpRequest()) {
                $ssnNumber = $this->params['ssnNumber'];
                $ssnInfo = Mage::helper('tele4G_sS4Integration')->getInfoBySsn($ssnNumber);
                $age = Mage::helper('tele4G_adform')->getAge($ssnNumber);
                $cartModel = Mage::getModel('tele4G_checkout/cart');
                if (
                    isset($ssnInfo->individualResponse->responseStatus->status) && 
                    $ssnInfo->individualResponse->responseStatus->status == 'OK'
                ) {                    
                    if($age < 7 ){
                        $response->setError('age<7');
                    } else if (($age < 16 ) and ($cartModel->ssnValidateByAge() or $cartModel->ssnValidateByAgePhoneType())) {                        
                        $response->setError('age<16');
                    } else if (($age < 18 ) and ($cartModel->ssnValidateByAge())) {
                        $response->setError('age<18');
                    } else{
                        $response->setError(false);
                        $response->setFirstName($ssnInfo->individualResponse->individual->firstName);
                        $response->setLastName($ssnInfo->individualResponse->individual->lastName);
                        $response->setAddressStreet($ssnInfo->individualResponse->individual->address->streetAddress);
                        $response->setAddressCity($ssnInfo->individualResponse->individual->address->city);
                        $response->setAddressPostalCode($ssnInfo->individualResponse->individual->address->postalCode);
                        Mage::getSingleton('checkout/session')->setDataFromSsn(serialize($ssnInfo->individualResponse->individual));
                    }
                } else {
                    Mage::log($ssnInfo, Zend_Log::INFO, 'ssn.log');
                    $response->setError($ssnInfo->individualResponse->responseStatus->errorCode);
                    $response->setMessage($ssnInfo->individualResponse->responseStatus->errorName);
                }
                //print_r($ssnInfo);
            } else {
                Mage::log('Request is not ajax', Zend_Log::INFO, 'ssn.log');
                $response->setError(true);
                $response->setMessage($this->__("Request is invalid!"));
            }
        } catch (Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
            Mage::logException($e);
        }

        return $this->getResponse()->setBody($response->toJSON());
    }
    
    /**
     * Log SSN
     */
    public function checkAction()
    {
        $ssn = $this->getRequest()->getParam('ssnNumber');
        Mage::helper('tele4G_sS4Integration')->ssnInfoLog($ssn);
    }
    
}
