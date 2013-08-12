<?php
class Tele4G_Payment_Block_Dibs_Cancel extends Mage_Core_Block_Template
{
    protected $_model = null;

    function getModel()
    {
        if (!$this->_model) {
            $this->_model = Mage::getModel('tele4G_payment/dibs');
        }
        return $this->_model;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tele4G/payment/dibs/cancel.phtml');
    }

    public function getCancelUrl()
    {
        return Mage::getUrl('checkout/tele4G', array('_secure'=>true));
    }
    
}
