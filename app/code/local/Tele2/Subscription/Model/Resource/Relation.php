<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Resource_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('tele2_subscription/relation', 'relation_id');
    }

    public function getSubscriptionProductIds($subscriptionId) 
    {

        $collection = Mage::getModel('tele2_subscription/relation')->getCollection()
            ->addFieldToFilter('subscription_id', $subscriptionId);
            //->joinProducts();
        
        $productIds = array();
        foreach ($collection as $relation)
        {
            if (!in_array($relation->getProductId(), $productIds))
                $productIds[] = $relation->getProductId();
        }
        return $productIds;
    }

    public function unlinkProduct($productId, $subscriptionId)
    {
        $st = Mage::getModel('core/resource')->getConnection('read')
          ->query("SELECT option_type_id FROM {$this->getTable('catalog/product_option_type_value')} otv INNER JOIN {$this->getTable('catalog/product_option')} po USING(option_id) LEFT JOIN {$this->getTable('tele2_subscription/relation')} tr ON(tr.option_value_id=otv.option_type_id)
             WHERE po.product_id='" . (int)$productId . "' AND tr.subscription_id='" . (int)$subscriptionId . "'");
        $optionValueIds = array();

        while ($row = $st->fetch())
        {
            $optionValueIds[] = $row['option_type_id'];
        }
        Mage::getModel('core/resource')->getConnection('write')
          ->query("DELETE FROM catalog_product_option_type_value WHERE option_type_id IN ('" . implode("', '", $optionValueIds) . "')");
    }
}
