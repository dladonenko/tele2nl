<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */


class Tele4G_Payment_Model_Method_CashOnDelivery extends Mage_Payment_Model_Method_Abstract
{
    protected $_code           = 'tele4G_cashondelivery';
    protected $_formBlockType  = 'tele4G_payment/form_cashOnDelivery';
    protected $_infoBlockType  = 'tele4G_payment/info_cashOnDelivery';
    protected $_canUseCheckout = true;

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return $this->getConfigData('can_use_checkout');
    }
}
