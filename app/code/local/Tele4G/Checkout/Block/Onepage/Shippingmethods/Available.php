<?php
/**
 * One page checkout Shipping block
 *
 * @category   Tele4G
 * @package    Tele4G_Checkout
 * @author     Ciklum
 */
class Tele4G_Checkout_Block_Onepage_Shippingmethods_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    protected $_resellerCities = null;
    
    public function isToGoMethod()
    {
        return Mage::getModel('tele4G_checkout/cart')->getIsFmcgOnly();
    }
    
    public function getResellerCities()
    {
        if (is_null($this->_resellerCities)) {
            $ss4IntegrationHelper = Mage::helper("tele4G_sS4Integration/data");
            $this->_resellerCities = $ss4IntegrationHelper->getResellerCities();
        }
        return $this->_resellerCities;
    }
    
    public function getSelectedCityToGo()
    {
        $cityToGoSelectedReseller = Mage::getSingleton('checkout/session')->getSelectedReseller();
        if (!$cityToGoSelectedReseller) {
            $cityToGoSelected = '';
        }
        $cityToGoSelected = Mage::getSingleton('checkout/session')->getSelectedResellerCity();
        if (!$cityToGoSelected) {
            $_dataFromSsn = Mage::getSingleton('checkout/session')->getDataFromSsn();
            $dataFromSsn = unserialize($_dataFromSsn);
            $cityToGoSelected = $dataFromSsn->address->city;
        }
        return $cityToGoSelected;
    }
}
