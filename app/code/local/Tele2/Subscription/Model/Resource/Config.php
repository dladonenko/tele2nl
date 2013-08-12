<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */


class Tele2_Subscription_Model_Resource_Config extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/subscription_config', 'config_id');
    }
}
