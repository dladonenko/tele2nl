<?php
class Tele4G_Togo_Model_Togo extends Mage_Core_Model_Abstract
{
    const BIND_PERIOD_24 = 24;
    const SHIPPING_METHOD_NAME = "togo";
    
    public function __construct()
    {
        $this->_init('tele4G_togo/togo');
    }
    
    public function getShippingMethodName()
    {
        return self::SHIPPING_METHOD_NAME . "_" . self::SHIPPING_METHOD_NAME;
    }
}