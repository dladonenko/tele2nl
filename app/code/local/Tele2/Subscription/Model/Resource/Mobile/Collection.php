<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Resource_Mobile_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/mobile');
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

    /**
     * Join mobile table
     *
     * @return Tele2_Subscription_Model_Resource_Mobile_Collection
     */
    public function joinAbstractSubscription()
    {
        $this->join(
            array('as' => 'tele2_subscription/subscription'),
            'main_table.subscription_id = as.subscription_id',
            array(
                'asa.price' => 'asa.price',
                'as.subscription_id' => 'as.subscription_id',
            )
        );
        return $this;
    }

    /**
     * Join flat tables
     *
     * @return Tele2_Subscription_Model_Resource_Mobile_Collection
     */
    public function joinFlatTables()
    {
        $this->join(
            array('asa' => 'tele2_subscription/subscription_attributes'),
            'main_table.subscription_id = asa.subscription_id'
        );
        $this->join(
            array('msa' => 'tele2_subscription/subscription_mobile_attributes'),
            'main_table.subscription_id = msa.subscription_id'
        );
        return $this;
    }

}
