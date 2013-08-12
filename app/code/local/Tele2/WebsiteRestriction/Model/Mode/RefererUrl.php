<?php

class Tele2_WebsiteRestriction_Model_Mode_RefererUrl 
{
    /*
     * This method 
     */
    public function getAuth()
    {
        $referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        //$referrer = "http://google.com";
        $serviceReferalUrl = Mage::app()->getStore()->getConfig('general/restriction/referrer_url');
        if (($referrer && preg_match('@^http[s]?://('.$serviceReferalUrl.')@i', $referrer))) {
            return $referrer;
        }
        return false;
    }
}
