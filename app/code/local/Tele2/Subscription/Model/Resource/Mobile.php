<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */


class Tele2_Subscription_Model_Resource_Mobile extends Tele2_Subscription_Model_Resource_Subscription
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/subscription_mobile', 'entity_id');
    }
}
