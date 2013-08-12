<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Resource_Relation_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/relation');
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

    public function joinProducts()
    {
        $select = $this->getSelect();
        $select->joinLeft(array('otv' => 'catalog_product_option_type_value'), 'otv.option_type_id=main_table.option_value_id');
        $select->joinLeft(array('po' =>  'catalog_product_option'), 'otv.option_id=po.option_id');

        return $this;
    }
}
