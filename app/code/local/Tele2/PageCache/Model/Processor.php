<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_PageCache
 */
class Tele2_PageCache_Model_Processor extends Enterprise_PageCache_Model_Processor
{
    /**
     * Populate request ids
     * @return Enterprise_PageCache_Model_Processor
     */
    protected function _createRequestIds()
    {
        $uri = $this->_getFullPageUrl();

        //Removing get params
        $pieces = explode('?', $uri);
        $uri = array_shift($pieces);

        /**
         * Define COOKIE state
         */
        if ($uri) {
            if (isset($_COOKIE['store'])) {
                $uri = $uri.'_'.$_COOKIE['store'];
            }
            if (isset($_COOKIE['currency'])) {
                $uri = $uri.'_'.$_COOKIE['currency'];
            }
            if (isset($_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_GROUP])) {
                $uri .= '_' . $_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_GROUP];
            }
            if (isset($_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN])) {
                $uri .= '_' . $_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN];
            }
            $uri .= $this->_getStoreCodes();
            if (isset($_COOKIE[Enterprise_PageCache_Model_Cookie::CUSTOMER_SEGMENT_IDS])) {
                $uri .= '_' . $_COOKIE[Enterprise_PageCache_Model_Cookie::CUSTOMER_SEGMENT_IDS];
            }
            if (isset($_COOKIE[Enterprise_PageCache_Model_Cookie::IS_USER_ALLOWED_SAVE_COOKIE])) {
                $uri .= '_' . $_COOKIE[Enterprise_PageCache_Model_Cookie::IS_USER_ALLOWED_SAVE_COOKIE];
            }
            $designPackage = $this->_getDesignPackage();

            if ($designPackage) {
                $uri .= '_' . $designPackage;
            }
        }

        $this->_requestId       = $uri;
        $this->_requestCacheId  = $this->prepareCacheId($this->_requestId);
        //Mage::log("FPS request id\n _requestId: {$this->_requestId}\n _requestCacheId: {$this->_requestCacheId}\n", 1, 'cache.uri.log');

        return $this;
    }
    
    protected function _getStoreCodes()
    {
        $_uri = '';
        $stores = Mage::app()->getStores(true);
        foreach ($stores as $store) {
            if (isset($_SESSION['core'][$store->getCode()])) {
                $_uri .= '_' . $_SESSION['core'][$store->getCode()];
            }
        }
        return $_uri;
    }
}
