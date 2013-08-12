<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Subscription extends Mage_Core_Model_Abstract
{
    /**
     * Skip save attributes
     *
     * @var bool
     */
    protected $_skipAttributesSaving;

    /**
     * Bindings list
     * @var array
     */
    protected $_bindings = array();

    /**
     * Addons collection
     * @var null|Tele2_Subscription_Model_Resource_AddonRelation_Collection
     */
    protected $_addons = null;

    /**
     * Config relation model
     * @var null|Tele2_Subscription_Model_ConfigRelation
     */
    protected $_configs = null;

    /**
     * Configs Subscription Collection
     *
     * @var getConfigsCollection
     */
    protected $_configsCollection = null;

    /**
     * Addon Collection
     *
     * @var getAddonCollection
     */
    protected $_addonCollection = null;

    /**
     * Subscription Collection
     *
     * @var getAllSubscriptions
     */
    protected $_subscriptionCollection = null;

    /**
     * Related product collection
     * @var null|Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_relatedProductCollection = null;

    /**
     * Subscription Old Data
     * @var null|array
     */
    protected $_subscriptionOldData = null;

    /**
     * Is can change a subscription price
     * @var null|bool
     */
    protected $_toChangeSubscriptionPrice = null;

    /**
     * Subscription type
     *
     * @var string
     */
    protected $_subscriptionType = null;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Tele2_Subscription_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param array $data
     * @param Mage_Core_Model_Config $config
     * @param Tele2_Subscription_Helper_Data $helper
     */
    public function __construct($data = array(), $config = null, $helper = null)
    {
        parent::__construct($data);
        $this->_config = ($config == null ? Mage::getConfig() : $config);
        $this->_helper = ($helper == null ? Mage::helper('tele2_subscription') : $helper);
        $this->_init('tele2_subscription/subscription');
    }

    /**
     * Get related products ids
     *
     * @return array
     */
    public function getRelatedProductsIds()
    {
        return $this->_config->getResourceModelInstance('tele2_subscription/relation')
            ->getSubscriptionProductIds($this->getSubscriptionId());
    }

    /**
     * Get related products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getRelatedProducts()
    {
        if (!$this->_relatedProductCollection && $this->getSubscriptionId()) {
            $relatedProductIds = $this->getRelatedProductsIds();
            $this->_relatedProductCollection = $this->_config->getResourceModelInstance('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addAttributeToSelect(array('options', 'subscription_group', 'subscription_type', 'subscription_type2'))
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter($this->getStoreId())
                ->addIdFilter($relatedProductIds);
//                ->addAttributeToSelect(array())
        }
        return $this->_relatedProductCollection;
    }

    /**
     * Retrieve subscription fake products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getSubscriptionProductsCollection()
    {
        $attributeSetName = Tele2_Install_Helper_Data::ATTR_SET_SUBSCRIPTION;
        $attributeSetId   = $this->_config->getModelInstance('eav/entity_attribute_set')
            ->load($attributeSetName, 'attribute_set_name')
            ->getAttributeSetId();

        $collection = $this->_config->getModelInstance('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId);

        return $collection;
    }

    /**
     * Retrieves collection of Subscriptions which are related to the product (e.g. device)
     *
     * @param mix $product
     * @throws Exception
     * @return Tele2_Subscription_Model_Subscription_Collection
     */
    public function getSubscriptionsByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } elseif (is_int($product) && $product > 0) {
            $productId = $product;
        } else {
            throw new Exception('Can not get subscriptions for this entity');
        }

        $subscriptionIds = $this->_config->getResourceModelInstance('tele2_subscription/relation_collection')
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToSelect('subscription_id')
            ->getColumnValues('subscription_id');

        $subscriptionIds = array_keys(array_flip($subscriptionIds));
        $subscriptions = array();
        if (count($subscriptionIds)) {
            foreach ($subscriptionIds as $key=>$id) {
                $subscriptionModel = $this->_config->getModelInstance('tele2_subscription/subscription');
                $subscriptions[$id] = $subscriptionModel->load($id);
            }
        }

        return $subscriptions;

    }

    /**
     * Save Relation
     * @param Mage_Catalog_Model_Product_Option $option
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _saveRelation($option, $product)
    {
        $this->_getResource()->saveRelation($option, $product, $this->getSubscriptionId());
    }

    /**
     * Get bindings
     *
     * @return false|array
     */
    public function getBindings()
    {
        if (isset($this->_bindings[$this->getSubscriptionId()])) {
            return $this->_bindings[$this->getSubscriptionId()];
        }

        $collection = $this->_config->getResourceModelInstance('tele2_subscription/binding_collection');
        $collection->filterBySubscription($this->getSubscriptionId());

        if (count($collection)) {
            foreach ($collection as $binding) {
                $this->_bindings[$this->getSubscriptionId()][] = $binding;
            }
            return $this->_bindings[$this->getSubscriptionId()];
        } else {
            return false;
        }
    }

    /**
     * getBindingById
     *
     * @param $bindingId
     * @return false|int
     */
    public function getBindingById($bindingId)
    {
        foreach ($this->getBindings() as $binding) {
            if ($bindingId == $binding->getBindingId()) {
                return $binding;
            }
        }
        return false;
    }

    /**
     * Get binding by period
     *
     * @param int $bindingPeriod
     * @return Tele2_Subscription_Model_Binding|false
     */
    public function getBindingByPeriod($bindingPeriod)
    {
        foreach ($this->getBindings() as $binding) {
            if ($bindingPeriod == $binding->getTime()) {
                return $binding;
            }
        }
        return false;
    }

    /**
     * Get Binding Times
     *
     * @return array
     */
    protected function _getBindingTimes()
    {
        $res = array();
        foreach ($this->getBindings() as $binding) {
            $res[] = $binding->getTime();
        }
        return $res;
    }

    /**
     * Get Bind Prices
     *
     * @return array
     */
    public function getBindPrices()
    {
        $bindPrices = array();
        foreach ($this->getBindings() as $binding) {
            if ($binding->getMonthlyPriceWithVat() > 0) {
                $bindPrices[$binding->getTime()] = $binding->getMonthlyPriceWithVat();
            } else {
                $bindPrices[$binding->getTime()] = $this->getData('price');
            }
        }
        return $bindPrices;
    }

    /**
     * Save Binding periods data
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return Tele2_Subscription_Model_Subscription
     */
    public function saveBinding($request)
    {
        $bindingParams = $request->getParam('new_binding');
        if ($bindingParams && $bindingParams['article_id']) {
            $binding = $this->_config->getModelInstance('tele2_subscription/binding');
            $binding->setSubscriptionId($this->getSubscriptionId())
              ->setTime($bindingParams['time'])
              ->setArticleId($bindingParams['article_id'])
              ->setMonthlyPriceWithVat($bindingParams['monthly_price_with_vat'])
              ->setMonthlyPriceWithoutVat($bindingParams['monthly_price_without_vat'])
              ->save();
        }
        $removeBinding = $request->getParam('remove_binding');
        if ($removeBinding) {
            foreach ($removeBinding as $bindingId => $on) {
                $this->_config->getModelInstance('tele2_subscription/binding')->load($bindingId)
                  ->delete();
            }
        }
        return $this;
    }

    /**
     * Save Associated Products
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @param array $updateData
     * @param array $subscriptionOldData
     * @return Tele2_Subscription_Model_Subscription
     */
    public function saveAssocProducts($request, $updateData = array(), $subscriptionOldData = array())
    {
        $in_products = $request->getPost('in_products');
        $links = $request->getPost('links');
        $related = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']);
        $related = array_keys($related);

        $allSelectedProducts = $request->getPost('selected_product');
        if (!is_array($allSelectedProducts)) {
            $allSelectedProducts = array();
        }
        $allSelectedProducts = array_merge($allSelectedProducts, $related);
        $oldSelectedProductsArray = $this->_config->getResourceModelInstance('tele2_subscription/relation')
            ->getSubscriptionProductIds($this->getSubscriptionId());
        if (count($allSelectedProducts)) {
            $addDiff = array_diff($allSelectedProducts, $oldSelectedProductsArray);     //just added to the subscription
        } else {
            $addDiff = $oldSelectedProductsArray;     //add all, all are new
        }
        $removeDiff = array_diff($oldSelectedProductsArray, $allSelectedProducts);  //to be removed from the subscription

        if ($removeDiff) {
            $this->_helper->unlinkProducts($removeDiff, $this->getSubscriptionId());
            $allSelectedProducts = array_diff($allSelectedProducts, $removeDiff);
        }

        if (isset($updateData['subscription_id'])) {
            $isSubsidyPriceChanged = $updateData['subsidy_price'] != $subscriptionOldData['subsidy_price'];
            $isSubscriptionPriceChanged = $updateData['price'] != $subscriptionOldData['price'];
            $isUpFrontPriceChanged = $updateData['up_front_price'] != $subscriptionOldData['up_front_price'];
            $isBindingsChanged = isset($subscriptionOldData['update_bindings']) ? true : false;
        } else {
            $isSubsidyPriceChanged = false;
            $isSubscriptionPriceChanged = false;
            $isUpFrontPriceChanged = false;
            $isBindingsChanged = false;
        }
        if ($isSubsidyPriceChanged || $isSubscriptionPriceChanged || $isUpFrontPriceChanged || $isBindingsChanged) {
            $this->_subscriptionOldData = $subscriptionOldData;
            $toChangePrice = $isSubscriptionPriceChanged || $isUpFrontPriceChanged ? true : false;
            $this->_toChangeSubscriptionPrice = $toChangePrice;
            $this->_processSelectedProducts(
                array('selected_product' => $allSelectedProducts),
                $this
            );
        } elseif(count($addDiff) && !$isSubsidyPriceChanged) {
            $this->_processSelectedProducts(
                array('selected_product' => $addDiff),
                $this
            );
        }
        return $this;
    }

    /**
     * Generate custom options for products
     * 
     * @param array $data
     * @param Tele2_Subscription_Model_Subscription $subscription
     * @return Tele2_Subscription_Model_Subscription
     */
    protected function _processSelectedProducts($data, $subscription)
    {
        if (isset($data['selected_product'])) {

            $selection = $data['selected_product'];
            foreach ($selection as $id => $productId) {
                if (!is_numeric($productId)) {
                    continue;
                }
                $this->_helper->generateCustomOptions($productId, $subscription);
            }
        }
        return $this;
    }

    /**
     * Retrive Subscription's Addons Collection
     * 
     * @return Tele2_Subscription_Model_Resource_AddonRelation_Collection
     */
    public function getAddons()
    {
        if (is_null($this->_addons)) {
            $this->_addons = $this->_config->getModelInstance('tele2_subscription/addonRelation')->getCollection()
                ->addFieldToFilter('subscription_id', $this->getSubscriptionId());
        }
        return $this->_addons;
    }

    /**
     * Retrieve Subscription's Addons Collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getAddonCollection()
    {
        if (is_null($this->_addonCollection)) {
            $addonIds = array();
            foreach ($this->getAddons() as $addon) {
                $addonIds[] = $addon->getAddonId();
            }
            if ($addonIds) {
                $this->_addonCollection = $this->_config->getModelInstance('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addIdFilter($addonIds);
            }

        }
        return $this->_addonCollection;
    }
    
    /**
     * Retrive Subscription's Configs Collection
     * 
     * @return Tele2_Subscription_Model_Resource_ConfigRelation_Collection
     */
    public function getConfigs()
    {
        if (is_null($this->_configs)) {
            $this->_configs = $this->_config->getModelInstance('tele2_subscription/configRelation')->getCollection()
                ->addFieldToFilter('subscription_entity_id', $this->getSubscriptionId());
        }
        return $this->_configs;
    }

    /**
     * Get subscription group
     *
     * @return Tele2_Subscription_Model_Group
     */
    public function getSubscriptionGroup()
    {
        return $this->_config->getModelInstance('tele2_subscription/group')->load($this->getData('subscription_group'));
    }

    /**
     * Override method load.
     * Use subscription_id as primary key instead of id
     *
     * @param int $id
     * @param null $field
     * @return Mage_Core_Model_Abstract
     */
    public function load($id, $field = null)
    {
        if ($field && $field == 'fake_product_id') {
            return $this->loadByFakeProduct($id);
        } else {
            return parent::load($id, $field ? $field : 'subscription_id');
        }
    }

    /**
     * Override method load.
     * Use subscription_id as primary key instead of id
     *
     * @param int $id
     * @param null $field
     * @return Mage_Core_Model_Abstract
     */
    public function loadByFakeProduct($productId)
    {
        if (is_array($productId)) {
            $productId = array_shift(array_values($productId));
        }
        $this->getResource()->loadByFakeProduct($this, $productId);
        return $this;
    }

    /**
     * Set Subscription Type
     *
     * @param string $subscriptionType
     */
    public function setSubscriptionType($subscriptionType)
    {
        $this->_subscriptionType = $subscriptionType;
    }

    /**
     * Get Subscription Type
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->_subscriptionType;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->getSubscriptionType()) {
            $baseSubscription = Mage::getModel('tele2_subscription/subscription');
            if ($this->getSubscriptionId()) {
                $baseSubscription->load($this->getSubscriptionId());
            }
            $baseSubscription->setData($this->getData());
            $baseSubscription->save();
            $this->setSubscriptionId($baseSubscription->getSubscriptionId());
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if (!$this->_skipAttributesSaving) {
            $subscriptionAttributes = Mage::getModel('tele2_subscription/SubscriptionAttributes');
            $subscriptionAttributesId = null;
            if ($this->getSubscriptionId()) {
                $subscriptionAttributes->load($this->getSubscriptionId(), 'subscription_id');
                $subscriptionAttributesId = $subscriptionAttributes->getId();
            }
            $subscriptionAttributes->setData($this->getData());
            $subscriptionAttributes->setId($subscriptionAttributesId);
            $subscriptionAttributes->save();
        }
        return parent::_afterSave();
    }


    /**
     * Processing object before delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {
        if ($this->getSubscriptionType()) {
            $subscription = $this->_config->getModelInstance('tele2_subscription/subscription')
                ->load($this->getSubscriptionId());
            $subscription->delete();
        }
        return parent::_beforeDelete();
    }

    /**
     * Retrieve Subscription's Configs Collection
     *
     * @return Tele2_Subscription_Model_Resource_Config_Collection
     */
    public function getConfigsCollection()
    {
        if (is_null($this->_configsCollection)) {
            $configIds = array();
            foreach ($this->getConfigs() as $config) {
                $configIds[] = $config->getConfigId();
            }
            if ($configIds) {
                $this->_configsCollection = Mage::getModel('tele2_subscription/config')->getCollection()
                    ->addFieldToFilter('config_id', array('in' => $configIds));
            }

        }
        return $this->_configsCollection;
    }
}
