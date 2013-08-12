<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */


class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Tab_Standalone
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Subscription instance
     * @var null | Tele2_Subscription_Model_Subscription
     */
    private $_subscription = null;
    
    /**
     * Subscription fake prodcut instance
     * @var null | Mage_Catalog_Model_Product
     */
    private $_fakeProduct = null;
    
    /**
    * All Subscription Products Collection
    * @var Mage_Catalog_Model_Resource_Product_Collection
    */
    private $_allSubscriptionProducts = null;
    
    /**
     * Catalog product 'subscription' attribute set id
     * @var null | int
     */
    private $_attributeSetId = null;

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('tele2_subscription')->__('Stand alone');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('tele2_subscription')->__('Stand alone');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrive current Subscription entity
     * 
     * @return Tele2_Subscription_Model_subscription
     */
    public function getSubscription()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = Mage::registry('subscription');
        }
        return $this->_subscription;
    }

    /**
     * Retrive Subscription's fake product name
     * 
     * @return string
     */
    public function getFakeProductTitle()
    {
        if ($this->getFakeProduct() && $this->getFakeProduct()->getId()) {
            return '<a href="'.Mage::getUrl('adminhtml/catalog_subscription/edit', array('id'=>$this->getFakeProduct()->getId())).'">'.$this->getFakeProduct()->getName().'</a>';
        } else {
            return 'No product selected';
        }
        return $this->_fakeProduct;
    }

    /**
     * Retrive catalog product 'subscription' attribute set id
     * 
     * @return int
     */
    public function getSubscriptionAttributeSetId()
    {
        if (is_null($this->_attributeSetId)) {
            $this->_attributeSetId = Mage::getModel('eav/entity_attribute_set')
                ->load('subscription', 'attribute_set_name')
                ->getAttributeSetId();
        }
        return $this->_attributeSetId;
    }

    /**
     * Retrive Subscription's fake product
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getFakeProduct()
    {
        if (is_null($this->_fakeProduct)) {
            if ($_fakeProductId = $this->getSubscription()->getFakeProductId()) {
                $this->_fakeProduct = Mage::getModel('catalog/product')->load($_fakeProductId);
            }
        }
        return $this->_fakeProduct;
    }

    /**
     * Retrive Subscription's fake product id
     * 
     * @return null | int
     */
    public function getFakeProductId()
    {
        if($this->getFakeProduct()) {
            return $this->getFakeProduct()->getId();
        } else {
            return null;
        }
    }

    /**
     * Retrieve subscription fake products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getAllSubscriptionProducts()
    {
        if (is_null($this->_allSubscriptionProducts)) {
            $this->_allSubscriptionProducts = Mage::getModel('tele2_subscription/mobile')
            ->getSubscriptionProductsCollection();
        }
        return $this->_allSubscriptionProducts;
    }

}
