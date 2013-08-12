<?php
/**
 * Tele2 FreeGift module
 *
 * @category    Tele2
 * @package     Tele2_FreeGift
 */
class Tele2_FreeGift_Model_Resource_FreeGift_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_freeGift/freeGift');
    }

    /**
     * Minimize usual count select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return parent::getSelectCountSql()->resetJoinLeft();
    }
}
