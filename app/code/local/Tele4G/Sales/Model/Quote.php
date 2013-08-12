<?php

class Tele4G_Sales_Model_Quote extends Mage_Sales_Model_Quote
{
	public function _construct()
    {
		$this->_init('sales/quote');
	}

	public function setDataSaveDeny()
    {
		$this->_dataSaveAllowed = false;
	}

    //
    public function getItemByProduct($product)
    {
        return false;
    }
}
