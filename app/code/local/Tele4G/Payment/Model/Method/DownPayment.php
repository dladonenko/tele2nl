<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */


class Tele4G_Payment_Model_Method_DownPayment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'tele4G_downpayment';
    protected $_formBlockType = 'tele4G_payment/form_downPayment';
    protected $_infoBlockType = 'tele4G_payment/info_downPayment';

}
