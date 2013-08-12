<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Resource_Binding_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/binding');
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

    public function filterBySubscription($subscriptionId)
    {
        $this->getSelect()
          ->where('subscription_id=?', (int)$subscriptionId);
       
        return $this;
    }
    
//    public function addBindings()
//    {
//        $this
//            ->getSelect()
//            ->joinLeft(
//                array('bind' => $this->getTable('tele2_subscription/binding')), 
//                'bind.subscription_id = main_table.subscription_id', 
//                array('binding_id', 'time', 'billing_code', 'invoice_price')
//            );
//        return $this;
//    }
}
