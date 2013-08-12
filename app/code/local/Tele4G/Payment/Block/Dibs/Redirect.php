<?php
class Tele4G_Payment_Block_Dibs_Redirect extends Mage_Core_Block_Template
{
    protected $_model;
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
        $this->setTemplate('tele4G/payment/dibs/redirect.phtml');
    }

    public function getActionUrl()
    {
        return Mage::getStoreConfig('payment/tele4G_dibs/entrypoint_url');
    }

    public function getCheckoutFormFields()
    {
        return $this->getModel()->getDibsRequestParameters();
    }
}
