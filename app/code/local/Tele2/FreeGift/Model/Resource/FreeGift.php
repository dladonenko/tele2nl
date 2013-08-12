<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_FreeGift
 */


class Tele2_FreeGift_Model_Resource_FreeGift extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_freeGift/tele2_freegift', 'entity_id');
    }
}
