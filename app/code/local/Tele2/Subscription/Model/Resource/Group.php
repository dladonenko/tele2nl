<?php

class Tele2_Subscription_Model_Resource_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct() 
    {
        $this->_init('tele2_subscription/group', 'group_id');
    }
}
