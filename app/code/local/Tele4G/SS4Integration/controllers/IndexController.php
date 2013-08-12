<?php
class Tele4G_SS4Integration_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function sendsmsAction()
    {
        $number = $this->getRequest()->getParam('number');
        if (!$number) {
            return;
        }
        
        $orders = Mage::getSingleton('checkout/session')->getQuote()->getOfferData();
        if(isset($orders)){
            $orders = unserialize($orders);
            foreach ($orders as $order){
                if((isset($order['number'])) && ($order['number'] == $number)){
                    $error = $this->__('error');
                    return $this->getResponse()->setBody(json_encode($error));
                }            
            }
        }
        
        $smsChallenge = Mage::helper('tele4G_sS4Integration')->sendChallengeSms($number);

        if (is_object($smsChallenge)) {
            $body = $smsChallenge->toJSON();
        } else {
            $body = null;
        }

        return $this->getResponse()->setBody($body);
    }

    public function validatecodeAction()
    {
        $number = $this->getRequest()->getParam('number');
        $number = preg_replace("/\D/","",$number);        
        $code = $this->getRequest()->getParam('code');
        $subId = $this->getRequest()->getParam('subid');

        if (!$number || !$code || !$subId) {
            return;
        }

        $service  = Mage::helper('tele4G_sS4Integration');
        $smsChallenge = $service->validateSmsCode($number, $code);

        $session = Mage::getSingleton('checkout/session');

        $responseObject = new Varien_Object();

        if (is_object($smsChallenge)) {//
            switch ($smsChallenge->result->result) {
                case 'SMS_VALIDATION_CODE_OK':
                    $subscription = Mage::getModel('tele2_subscription/mobile')->load($subId);
                    if ($subscription && $subscription->getId() && $subscription->getPriceplan()) {
                        $atResponse = $service->getATypeForExistNum($number, $subscription->getPriceplan());
                        if ($atResponse->result->responseStatus->status == 'ERROR') {
                            $session->unsActivationType();
                            $session->unsActivationNumber();
                            $responseObject->error = $service->getErrorFromXML($atResponse->result->responseStatus->errorName);
                        } elseif($atResponse->result->responseStatus->status == 'OK' && isset($atResponse->result->activationType)) {
                            $session->setActivationExistType($atResponse->result->activationType);
                            $session->setActivationExistNumber($number);
                            $responseObject->result = $this->__('Koden godkänd');
                            $responseObject->type = $atResponse->result->activationType;
                            // $responseObject->result = $this->__('Activation Type ' . $atResponse->result->activationType);
                        }
                    }
                    break;

                case 'SMS_MAX_CODE_VERIFICATION_ATTEMPTS':
                    $session->unsActivationType();
                    $session->unsActivationNumber();
                    $responseObject->error = $service->getErrorFromXML($smsChallenge->result->result);
                    break;

                case 'SMS_VERIFICATION_CODE_NOT_GENERATED':
                    $session->unsActivationType();
                    $session->unsActivationNumber();
                    $responseObject->error = $service->getErrorFromXML($smsChallenge->result->result);
                    break;

                default://SMS_INVALID_VERIFICATION_CODE etc
                    $session->unsActivationType();
                    $session->unsActivationNumber();
                    $responseObject->error = $service->getErrorFromXML('SMS_INVALID_VERIFICATION_CODE');
                    break;
            }
            $body = $responseObject->toJSON();
        } else {
            $body = null;
        }

        return $this->getResponse()->setBody($body);
    }

    /**
     * Validate Number for Purchant Assistant
     * and get activation type
     */
    public function validatenumberAction()
    {
        $number = $this->getRequest()->getParam('number');
        $subId = $this->getRequest()->getParam('subid');
        if (!$number || !$subId) {
            return;
        }

        $orders = Mage::getSingleton('checkout/session')->getQuote()->getOfferData();
        if(isset($orders)){
            $orders = unserialize($orders);
            foreach ($orders as $order){
                if((isset($order['number'])) && ($order['number'] == $number)){
                    $error = $this->__('error');
                    return $this->getResponse()->setBody(json_encode($error));
                }            
            }
        }

        $subscription = Mage::getModel('tele2_subscription/mobile')->load($subId);        

        /** Get Activation Type */
        $service  = Mage::helper('tele4G_sS4Integration');
        $session = Mage::getSingleton('checkout/session');
        $responseObject = new Varien_Object();

        $atResponse = $service->getATypeForExistNum($number, $subscription->getPriceplan());
        if ($atResponse->result->responseStatus->status == 'ERROR') {
            $session->unsActivationType();
            $session->unsActivationNumber();
            $responseObject->error = $service->getErrorFromXML($atResponse->result->responseStatus->errorName);
        } elseif($atResponse->result->responseStatus->status == 'OK' && isset($atResponse->result->activationType)) {
            $session->setActivationExistType($atResponse->result->activationType);
            $session->setActivationExistNumber($number);
            $responseObject->result = $this->__('Ok');
            //$responseObject->result = $this->__('Koden godkänd'); //The code approved
            $responseObject->type = $atResponse->result->activationType;
        }

        if (is_object($responseObject)) {
            $body = $responseObject->toJSON();
        } else {
            $body = null;
        }
        return $this->getResponse()->setBody($body);
    }

    public function availablePhoneNumbersAction()
    {
        $availableNumbers = Mage::helper('tele4G_sS4Integration')->getAvailablePhoneNumbers(5);
        if (is_array($availableNumbers)) {
            $body = json_encode(array('status' => "OK", "response" => $availableNumbers));
        } else {
            $body = json_encode(array('status' => "ERROR", "error_message" => "Tjänsten är inte tillgänglig"));
        }
        return $this->getResponse()->setBody($body);
    }
    
}



