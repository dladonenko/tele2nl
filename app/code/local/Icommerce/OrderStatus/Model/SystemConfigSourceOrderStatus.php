<?php

class Icommerce_OrderStatus_Model_SystemConfigSourceOrderStatus extends 
      Mage_Adminhtml_Model_System_Config_Source_Order_Status {
    public function __construct() { 
        $this->_stateStatuses[] = "pay_pending";
        $this->_stateStatuses[] = "captured";
        $this->_stateStatuses[] = "pay_failed";
    }
}
