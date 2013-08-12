<?php
class Tele4G_Payment_Block_Auriga_Success extends Mage_Core_Block_Template
{
    protected $_model;
    public function getAuriga()
    {
        if (is_null($this->_model))
            $this->_model = Mage::getModel('tele4G_payment/auriga');
        return $this->_model;
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tele4G/payment/auriga/success.phtml');
    }
}

