<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele4G_Subscription_Model_Relation extends Mage_Core_Model_Abstract
{

    /**
     * Product collection
     *
     * @var null|Mage_Catalog_Model_Resource_Product_Collection
     */
    private $_productCollection = null;

    public function __construct()
    {
        $this->_init('tele2_subscription/relation');
    }

    /**
     * Retrieves products from different combination of type1/type2 subscription attribute
     *
     * @param string $subscriptionType1Id
     * @param string $subscriptionType2Id
     * @param null|int $_categoryId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    
    public function getProductsBySubscriptionGroup( $subscriptionType2Id, $_categoryId = null)
    {
        if (!$this->_productCollection) {                       
            $this->_productCollection = Mage::getModel('catalog/product')->getCollection()
                //->addIdFilter($pdo)
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setOrder('position','asc')
                ;            
            
           //remove filter by pre/post subscription
           //->where("subscr.type1 = $subscriptionType1Id")
            
            $this->_productCollection->getSelect()
                    ->joinInner(array('relation'=> 'tele2_relation'), 'relation.product_id = e.entity_id',array('relation.subscription_id'))
                    ->joinInner(array('subscr'=> 'tele2_mobile_subscription'), 'relation.subscription_id = subscr.subscription_id', array('subscr.type2','subscr.type1'))
                    ->where("subscr.type2 in ($subscriptionType2Id)")                    
                    ->group('e.entity_id')
                    ;  
            
            //echo $this->_productCollection->getSelect()->assemble(); die();
            if (!is_null($_categoryId)) {
                $_category = Mage::getModel('catalog/category')->load($_categoryId);
                $this->_productCollection->addCategoryFilter($_category);
            }
              
        }
        return $this->_productCollection;
    }
}
