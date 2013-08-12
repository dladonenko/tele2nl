<?php
class Icommerce_Auriga_Block_Failure extends Mage_Core_Block_Template {
	// Dead-beat detected.

    protected $_model;
    function getAuriga()
    {
      if (is_null($this->_model))
        $this->_model = Mage::getModel('auriga/auriga');
      return $this->_model;
    }

    protected function _construct()
    {
      parent::_construct();
      $this->setTemplate('icommerce/auriga/failure.phtml');
    }
}
