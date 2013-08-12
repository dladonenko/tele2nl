<?php

class Tele4G_Common_Model_Observer
{

    public function setCountItemsCartToCookie()
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            $count_items = Mage::helper('tele4G_checkout')->getSummaryCount();
            $cookie_domain = Mage::getStoreConfig('tele4G/variables/domain_cookie');
            $cookie = Mage::getSingleton('core/cookie');
            $cookie->set('CartItems', $count_items, 0, '/', $cookie_domain, null, false);
        }
    }
}