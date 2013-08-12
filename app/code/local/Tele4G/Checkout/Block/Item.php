<?php


class Tele4G_Checkout_Block_Item extends Mage_Checkout_Block_Cart
{

    private $session;
    private $params;
    
    private $_addonCollection = null;

    public function __construct()
    {
        $this->session = Mage::getSingleton('checkout/session');
    }

    public function getOffer()
    {
        if ($this->session->getOfferParamsAfterCart()) {
            $this->params = $this->session->getOfferParamsAfterCart();
            $product = Mage::getModel('catalog/product')->load($this->params['product']);
            return $product;
        }
        return false;
    }

    public function isOfferDevice()
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if (is_object($this->getOffer())) {
            $attributeSetName = $this->_getAttributeSetNameById($this->getOffer()->getAttributeSetId());
            if ($attributeSetName == $helperCommon::ATTR_SET_DEVICE) {
                return true;
            }
        }
        return false;
    }
    
    public function isOfferDongle()
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if (is_object($this->getOffer())) {
            $attributeSetName = $this->_getAttributeSetNameById($this->getOffer()->getAttributeSetId());
            if ($attributeSetName == $helperCommon::ATTR_SET_DONGLE) {
                return true;
            }
        }
        return false;
    }

    public function isOfferSubscription()
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if (is_object($this->getOffer())) {
            $attributeSetName = $this->_getAttributeSetNameById($this->getOffer()->getAttributeSetId());
            if ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                return true;
            }
        }
        return false;
    }

    public function getCompatibleAddon()
    {
        $aAddonIds = array();
        if (is_null($this->_addonCollection)) {
            if ($this->isOfferDevice()) {
                if (isset($this->params['options'])) {
                    $value = Mage::getModel('catalog/product_option_value')->load(current($this->params['options']));
                    $sku = $value->getSku();
                    $aSubscriptionBT = array();
                    $foundSBT = '';

                    if (preg_match('%subscr-(\d+)-(\d+)%', $sku, $foundSBT)) {
                        if (is_array($foundSBT)) {
                            $subscription_id = $foundSBT[1];

                            $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                            $relations = Mage::getModel('tele2_subscription/addonRelation')->getCollection()
                                ->addFieldToFilter('stype_id', $_subscription->getType1())
                                ->addFieldToFilter('subscription_id', $subscription_id)
                                ->addFieldToSelect("subscription_id")
                                    ->addFieldToSelect("addon_id");
                            foreach ($relations as $rel) {
                                    $aAddonIds[] = $rel->getAddonId();
                            }
                        }
                    }
                }
                
            }

            if ($this->isOfferSubscription()) {
                $subscription_id =  Mage::helper("tele4G_checkout/data")->getSubscriptionIdByproduct($this->getOffer()->getId());
                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                $relations = Mage::getModel('tele2_subscription/addonRelation')->getCollection()
                    ->addFieldToFilter('stype_id', $_subscription->getType1())
                    ->addFieldToSelect("subscription_id")
                    ->addFieldToSelect("addon_id");
                
                foreach ($relations as $rel) {
                    if (is_null($rel->getSubscriptionId()) || $rel->getSubscriptionId() == $subscription_id) {
                        $aAddonIds[] = $rel->getAddonId();
                    }
                }
            }

            if (!empty($aAddonIds)) {
                $this->_addonCollection = Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $aAddonIds))
                    ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('monthly_price')
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('addon_group')
                    ->setOrder('addon_group', 'DESC');
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_addonCollection);
            }
        }
        return $this->_addonCollection;
    }

    public function getCompatibleAccessories()
    {
        if (is_object($this->getOffer())) {
            $accessoriesAttributeSetId = Mage::getModel('eav/entity_attribute_set')
                ->load(Tele4G_Common_Helper_Data::ATTR_SET_ACCESSORY, 'attribute_set_name')
                ->getAttributeSetId();

            $_relatedCollection = $this->getOffer()->getRelatedProductCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('attribute_set_id', $accessoriesAttributeSetId)
                ->addAttributeToSort('position', 'asc')
                ->addStoreFilter();

            if ($_relatedCollection->getItems()) {
                return $_relatedCollection;
            }
        }
        return false;
    }
    
    public function getCompatibleInsurances()
    {
        if (is_object($this->getOffer())) {
            $insuranceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
                ->load(Tele4G_Common_Helper_Data::ATTR_SET_INSURANCE, 'attribute_set_name')
                ->getAttributeSetId();
            
            $_relatedCollection = $this->getOffer()->getRelatedProductCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('attribute_set_id', $insuranceAttributeSetId)
                ->addAttributeToSort('position', 'asc')
                ->addStoreFilter();

            if ($_relatedCollection->getItems()) {
                return $_relatedCollection;
            }
        }
        return false;
    }

    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }
}
