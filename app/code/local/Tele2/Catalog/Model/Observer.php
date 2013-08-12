<?php

class Tele2_Catalog_Model_Observer
{
    /**
     * Redirect from Category in case non empty Redirect Url attribute
     *
     * @param   Varien_Event_Observer $observer
     * @return  Tele2_Catalog_Model_Observer
     */
    public function catalogRedirect(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($redirectUrl = $category->getRedirectUrl()) {
            if (preg_match('@^(?:http://)([^/]+)@i', $redirectUrl, $matches)) {
                Mage::app()->getResponse()->setRedirect($redirectUrl);
            } else {
                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl().$redirectUrl);
            }
        }
    }
    
}
