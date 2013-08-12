<?php
class Tele2_FreeGift_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_subscriptions        = array();
    protected $_subscriptionsObjects = array();
    protected $_binding              = array();
    protected $_products             = array();
    protected $_productCollection    = array();

    protected function _getProductsCollection($attributeSet)
    {
        if (!isset($this->_productCollection[$attributeSet])) {
            $attributeSetId   = Mage::getModel('eav/entity_attribute_set')
                ->load($attributeSet, 'attribute_set_name')
                ->getAttributeSetId();

            $this->_productCollection[$attributeSet] = Mage::getModel('catalog/product')->getCollection()
//                ->addAttributeToSelect('*')
                ->addAttributeToSelect('name')
                ->addFieldToFilter('attribute_set_id', $attributeSetId);
            if ($attributeSet == Tele2_Install_Helper_Data::ATTR_SET_DEVICE) {
                $this->_productCollection[$attributeSet]
                    ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
            }
        }
        return $this->_productCollection[$attributeSet];
    }

    public function getProductsAsOptions($productType, $withEmptyOption = false)
    {
        if (!isset($this->_products[$productType])) {
            if ($withEmptyOption) {
                $this->_products[$productType] = array(array(
                    'value' => 0,
                    'label' => 'Please select'
                ));
            } else {
                $this->_products[$productType] = array();
            }
            $productsCollection = $this->_getProductsCollection($productType);
            foreach ($productsCollection as $product) {
                $this->_products[$productType][] = array(
                    'value' => $product->getId(),
                    'label' => $product->getName()
                );
            }
        }
        return $this->_products[$productType];
    }

    public function getSubscriptionAsOptions($withEmptyOption = false)
    {
        if (!count($this->_subscriptions)) {
            if ($withEmptyOption) {
                $this->_subscriptions = array(array(
                    'value' => 0,
                    'label' => 'Please select'
                ));
            } else {
                $this->_subscriptions = array();
            }
            foreach (Mage::getModel('tele2_subscription/subscription')->getCollection() as $subscription) {
                $this->_subscriptions[] = array(
                    'value' => $subscription->getId(),
                    'label' => $subscription->getName()
                );
            }
        }
        return $this->_subscriptions;
    }

    public function getBindingPeriods($subscriptionId)
    {
        if (!$subscriptionId) {
            return array();
        }
        if (!isset($this->_binding[$subscriptionId])) {
            if (!isset($this->_subscriptionObjects[$subscriptionId])) {
                $this->_subscriptionObjects[$subscriptionId] = Mage::getModel('tele2_subscription/subscription')
                    ->load($subscriptionId);
            }
            
            $this->_binding[$subscriptionId] = array();
            foreach ($this->_subscriptionObjects[$subscriptionId]->getBindings() as $binding) {
                $this->_binding[$subscriptionId][] = array(
                    'value' => $binding->getBindingId(),
                    'label' => $binding->getTime()
                );
            }
        }
        return $this->_binding[$subscriptionId];
    }
}
