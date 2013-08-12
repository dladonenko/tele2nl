<?php
/**
 * Magento Enterprise Edition
 * 
 * @category    Tele2
 * @package     Tele2_WebsiteRestriction
 */

/**
 * Private sales and stubs observer
 *
 */
class Tele2_WebsiteRestriction_Model_Observer extends Enterprise_WebsiteRestriction_Model_Observer
{
    /**
     * Implement website stub or private sales restriction
     *
     * @param Varien_Event_Observer $observer
     */
    public function restrictWebsite($observer)
    {
        parent::restrictWebsite($observer);

        /* @var $controller Mage_Core_Controller_Front_Action */
        $controller = $observer->getEvent()->getControllerAction();

        if (!Mage::app()->getStore()->isAdmin()) {
            $dispatchResult = new Varien_Object(array('should_proceed' => true, 'customer_logged_in' => false));
            Mage::dispatchEvent('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!Mage::helper('enterprise_websiterestriction')->getIsRestrictionEnabled()) {
                return;
            }
            /* @var $request Mage_Core_Controller_Request_Http */
            $request    = $controller->getRequest();
            /* @var $response Mage_Core_Controller_Response_Http */
            //$response   = $controller->getResponse();
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();

            switch ((int)Mage::getStoreConfig(Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE)) {
                case Tele2_WebsiteRestriction_Model_Mode::ALLOW_COOKIE:
                    // to Main Website
                    $cookieName = Mage::getStoreConfig(Tele2_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_COOKIE_NAME);
                    $cookieValue = Mage::getStoreConfig(Tele2_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_COOKIE_VALUE);
                    $cookieCheckValue = Mage::getStoreConfig(Tele2_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_COOKIE_CHECK_VALUE);

                    if (!(
                        isset($_COOKIE[$cookieName]) &&
                        (
                            !$cookieCheckValue ||
                            ($cookieCheckValue && $_COOKIE[$cookieName] == $cookieValue)
                        )
                    )) {
                        $this->_redirect($controller);
                    }
                    break;
                case Tele2_WebsiteRestriction_Model_Mode::ALLOW_URL_REQUEST:
                        $requestAttribute = Mage::app()->getStore()->getConfig('general/restriction/request_attribute');
                        $sessionVariable = Mage::app()->getStore()->getConfig('general/restriction/request_session_variable');

                        $sessionValue = Mage::getSingleton('core/session')->getData($sessionVariable);
                        $requestUId = Mage::app()->getRequest()->getParam($requestAttribute);
                        Mage::getSingleton('enterprise_pagecache/cookie')->set(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE, md5(time()));
                        $_referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

                        if ($sessionValue) {
                            //Extend Session
                            Mage::log("\nSuccessfull second attempt\n sessionValue '{$sessionVariable}': {$sessionValue}\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "mecenat.redirects.second_attempt.log");
                        } elseif($requestUId) {
                            //Make request for authentication
                            if (Mage::getModel('tele2_websiterestriction/mode_session')->getAuth($requestUId)) {
                                //Set Session
                                Mage::getSingleton('core/session')->setData($sessionVariable, $requestUId);
                                Mage::log("\nauth ok\n set session ({$sessionVariable} = {$requestUId})\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "mecenat.redirects.auth.log");
                                $controller->getResponse()->setRedirect($currentUrl);
                            } else {
                                //Redirect to landing page
                                Mage::log("\n fail auth\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "mecenat.redirects.main_site.log");
                                $this->_redirect($controller);
                            }
                        } else {
                            Mage::log("\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "mecenat.redirects.main_site.log");
                            $this->_redirect($controller);
                        }
                    break;
                case Tele2_WebsiteRestriction_Model_Mode::ALLOW_REFERRER_URL:
                    $sessionVariable = Mage::app()->getStore()->getConfig('general/restriction/referrer_session_variable');
                    $sessionValue = Mage::getSingleton('core/session')->getData($sessionVariable);
                    Mage::getSingleton('enterprise_pagecache/cookie')->set(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE, md5(time()));

                        if ($sessionValue) {
                            //Exist Session
                            $_referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                            Mage::log("\nSuccessfull second attempt\n sessionValue: {$sessionValue}\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "redirects.second_attempt.log");
                        } elseif($referrer = Mage::getModel('tele2_websiterestriction/mode_refererUrl')->getAuth()) {
                            //Make request for authentication
                            //Set Session
                            Mage::getSingleton('core/session')->setData($sessionVariable, md5(time()));
                            Mage::log("\nauth ok, set session\n targetUrl: {$currentUrl}\n referrer: {$referrer}\n", 1, "redirects.auth.log");
                            $controller->getResponse()->setRedirect($currentUrl);
                        } else {
                            $_referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                            Mage::log("\n targetUrl: {$currentUrl}\n referrer: {$_referrer}\n", 1, "redirects.main_site.log");
                            $this->_redirect($controller);
                        }
                    break;
            }
        }
    }

    private function _redirect($controller)
    {
        /* @var $response Mage_Core_Controller_Response_Http */
        $response   = $controller->getResponse();
        $pageIdentifier = Mage::getStoreConfig(
                Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_LANDING_PAGE
            );
        if ($pageIdentifier === 'root') {
            $redirectUrl = Mage::app()->getStore(0)->getBaseUrl('web');
        } else {
            $redirectUrl = Mage::getUrl('', array('_direct' => $pageIdentifier));
        }

        if ($redirectUrl) {
            $response->setRedirect($redirectUrl);
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
        $afterLoginUrl = Mage::getUrl();
        Mage::getSingleton('core/session')->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
    }
}
