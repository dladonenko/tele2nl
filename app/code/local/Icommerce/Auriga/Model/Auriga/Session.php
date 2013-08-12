<?php

class Icommerce_Auriga_Model_Auriga_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('auriga');
    }
}
