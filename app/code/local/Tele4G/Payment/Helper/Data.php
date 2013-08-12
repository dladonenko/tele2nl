<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */
class Tele4G_Payment_Helper_Data extends Mage_Payment_Helper_Data
{
    public function getDibsUrl()
    {
        return Mage::getUrl('payment/dibs/index/');
    }
    
}
