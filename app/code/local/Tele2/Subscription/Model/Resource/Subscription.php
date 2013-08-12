<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */


class Tele2_Subscription_Model_Resource_Subscription extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/subscription', 'subscription_id');
    }

    /**
     * Overrade method retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinInner(
            array('as' => 'tele2_abstract_subscription'),
            $this->getMainTable() . '.subscription_id=as.subscription_id'
        );

        $select->joinInner(
            array('asa' => 'tele2_abstract_subscription_attributes'),
            $this->getMainTable() . '.subscription_id=asa.subscription_id'
        );

        $select->joinInner(
            array('msa' => 'tele2_mobile_subscription_attributes'),
            $this->getMainTable() . '.subscription_id=msa.subscription_id'
        );

        return $select;
    }


    public function saveRelation($option, $product, $subscriptionId)
    {
        $values = $option->getValuesCollection();
        foreach ($values as $value)
        {
            $sku = $value->getSku();
            /*
             *  Changed, to save option with all values for all subscriptions, not current only
            //if (preg_match("%subscr-" . $subscriptionId . "-(\d+)%", $sku, $m)) {
             */
            if (preg_match("%subscr-(\d+)-(\d+)%", $sku, $m)) {
                $relation = Mage::getModel('tele2_subscription/relation');
                $relation->setOptionValueId($value->getId())
                    ->setSubscriptionId($m[1])
                    ->setBindingTime($m[2])
                    ->setProductId($product->getId());
                $relation->save();
            }
        }
    }

    public function loadByFakeProduct($object, $productId)
    {
        $field = 'tele2_abstract_subscription.fake_product_id';

        $read = $this->_getReadAdapter();
        if ($read && !is_null($productId)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->joinInner(
                    array('as' => 'tele2_abstract_subscription'),
                    $this->getMainTable() . '.subscription_id=as.subscription_id'
                )
                ->where('as.fake_product_id' . '=?', $productId)
                ->joinInner(
                    array('asa' => 'tele2_abstract_subscription_attributes'),
                    $this->getMainTable() . '.subscription_id=asa.subscription_id'
                )
                ->joinInner(
                    array('msa' => 'tele2_mobile_subscription_attributes'),
                    $this->getMainTable() . '.subscription_id=msa.subscription_id'
                );


            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }
}
