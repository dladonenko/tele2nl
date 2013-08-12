<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */

class Tele4G_Payment_Block_Info_CashOnDelivery extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/default.phtml');
    }

}