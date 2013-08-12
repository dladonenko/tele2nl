<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */


class Tele4G_Payment_Model_Method_Invoice extends Mage_Payment_Model_Method_Checkmo
{

    protected $_code  = 'tele4G_invoice';
    protected $_formBlockType = 'payment/form_checkmo';
    protected $_infoBlockType = 'payment/info_checkmo';

}
