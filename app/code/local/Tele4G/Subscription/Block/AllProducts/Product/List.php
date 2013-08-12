<?php
/**
 * AllProducts list block
 *
 * @category   Tele4G
 * @package    Tele4G_Subscription
 */
class Tele4G_Subscription_Block_AllProducts_Product_List extends Mage_Catalog_Block_Product_List
{

    /**
     * Devices Attribute Set name
     * @var string
     */
    protected $_deviceAttributeSetName = "device";

    /**
     * Subscription products collection
     * @var null | Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_subscriptionCollection = null;

    /**
     * Related products collection
     * @var null | Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_itemRelatedCollection = null;

    /**
     * Default toolbar block name
     * @var string
     */
    protected $_defaultToolbarBlock = 'tele4G_subscription/allProducts_product_list_toolbar';

    public function getCacheKeyInfo()
    {
        $info = parent::getCacheKeyInfo();
        $info['cat_id'] = $this->_getCatId();
        if (isset($_GET['mode'])) {
            $info['mode'] = $_GET['mode'];
        }
        return $info;
    }

    public function getCacheLifetime()
    {
        return 7200;
    }

    protected function _getCatId()
    {
        if ($this->hasData('_cat_id')) {
            return $this->getData('_cat_id');
        }
        $catId = 0;
        if ($currentCategory = Mage::registry('current_category')) {
            $catId = $currentCategory->getId();
        } else {
            $catId = Mage::getModel('catalog/category')->loadByAttribute('code', 'configurable_devices')->getId();
        }
        $this->setData('_cat_id', $catId);
        return $catId;
    }

    public function getSubscriptionCollection()
    {
        if (is_null($this->_subscriptionCollection)) {
            $subscription = Mage::getModel('tele2_subscription/mobile');
            $this->_subscriptionCollection = Mage::getResourceModel('tele2_subscription/mobile_collection');
            if ($mode = $this->getRequest()->getParam('mode')) {
                if ('pre' == strtolower($mode)) {
                    $this->_subscriptionCollection
                        ->addFieldToFilter('type1', $subscription::SUBSCRIPTION_TYPE1_PRE);
                } elseif ('post' == strtolower($mode)) {
                    $this->_subscriptionCollection
                        ->addFieldToFilter('type1', $subscription::SUBSCRIPTION_TYPE1_POST);
                }
            }
        }
        return $this->_subscriptionCollection;
    }

    /**
     * Return related products
     *
     * @param array $subscriptions
     * $return array
     */
    public function getRelatedProducts(&$subscriptions)
    {
        $allRelatedProducts = array();
        foreach ($subscriptions as $subscriptionGroup => $_subscriptions) {
            $_productIds = array();
            if (isset($_subscriptions['subscription'])) {
                foreach ($_subscriptions['subscription'] as $_subscription) {
                    if(!isset($allRelatedProducts[$subscriptionGroup])) {
                        $allRelatedProducts[$subscriptionGroup] = array();
                    }
                    foreach($this->getRelatedProductCollection($_subscription) as $_relatedProduct) {
                        if(!in_array($_relatedProduct->getId(), $_productIds)) {
                            $allRelatedProducts[$subscriptionGroup][] = $_relatedProduct;
                            $_productIds[] = $_relatedProduct->getId();
                        }
                    }
                }
            }
        }
        foreach ($allRelatedProducts as $subscriptionGroup => $_relatedProducts) {
            if (count($_relatedProducts)) {
                $this->_qSort($_relatedProducts, 'position');
            }
            $subscriptions[$subscriptionGroup]['related_products'] = $_relatedProducts;
        }
    }

    /**
     * Retrive related products
     *
     * @param $product Mage_Catalog_Model_Product
     */
    public function getRelatedProductCollection($product)
    {
        $categoryCode = Mage::app()->getStore()->getConfig('tele4G_subscription/category_devices');
        $category = Mage::getModel('catalog/category')->loadByAttribute('code', $categoryCode);

        $this->_itemRelatedCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options', 'position')
        //->setPositionOrder()
            ->addStoreFilter()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter($this->getStoreId())
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
            ->addCategoryFilter($category)
            ->addAttributeToSort('position')
        ;

        $this->_filterDevice($this->_itemRelatedCollection);

        $this->_addProductAttributesAndPrices($this->_itemRelatedCollection);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_itemRelatedCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemRelatedCollection);

        //foreach ($this->_itemRelatedCollection as $product) {
        //    $product->setDoNotUseCategoryId(true);
        //}

        $products = array();
        foreach ($this->_itemRelatedCollection as $_product) {
            $products[] = $_product;
        }

        return $products;
    }

    public function getDevicesBySubscriptionGroup($_catID)
    {
        $subscription = Mage::getModel('tele2_subscription/mobile');
        $deviceCollection = Mage::getModel('tele4G_subscription/relation');

        
        
        $subscriptionType2Id = array($subscription::SUBSCRIPTION_TYPE2_S,$subscription::SUBSCRIPTION_TYPE2_M,$subscription::SUBSCRIPTION_TYPE2_L);
        $subscriptionType2Id = implode(',', $subscriptionType2Id); 
        $allProductsByGroups = $deviceCollection->getProductsBySubscriptionGroup($subscriptionType2Id, $_catID);
        $deviceGroupArray = array(
            'SUBSCRIPTION_TYPE2_S' => array(),
            'SUBSCRIPTION_TYPE2_M' => array(),
            'SUBSCRIPTION_TYPE2_L' => array(),
        );
        foreach ($allProductsByGroups as $item){
            if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_S ){
                $deviceGroupArray['SUBSCRIPTION_TYPE2_S'][] = $item;
            } else if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_M){
                $deviceGroupArray['SUBSCRIPTION_TYPE2_M'][] = $item;
            } else if($item->getType2() == $subscription::SUBSCRIPTION_TYPE2_L){
                $deviceGroupArray['SUBSCRIPTION_TYPE2_L'][] = $item;
            }                    
        }
        
        return array($deviceGroupArray['SUBSCRIPTION_TYPE2_S'],$deviceGroupArray['SUBSCRIPTION_TYPE2_M'],$deviceGroupArray['SUBSCRIPTION_TYPE2_L']);
    }

    /**
     * Add filter only devices to collection
     *
     * @param $collection
     * @return Tele4G_Catalog_Block_AllProducts_Product_List
     */
    protected function _filterDevice($collection)
    {
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($this->_deviceAttributeSetName, 'attribute_set_name')
            ->getAttributeSetId();

        $collection->addFieldToFilter('attribute_set_id', $attributeSetId);
        return $this;
    }

    /**
     * Quick array sorting for relative products array
     *
     * @param        $array         Array to sort
     * @param string $sortAttribute Attribute for sorting
     */
    protected function _qSort(&$array, $sortAttribute = 'position')
    {
        $left = 0;
        $right = count($array) - 1;

        $this->_mySort($array, $left, $right, $sortAttribute);
    }

    /**
     * Recursive method for quick sort
     *
     * @param $array
     * @param $left
     * @param $right
     * @param $sortAttribute
     */
    protected function _mySort(&$array, $left, $right, $sortAttribute)
    {
        $l = $left;
        $r = $right;
        $center = $array[(int)($left + $right) / 2]->getData($sortAttribute);
        do {
            while ($array[$r]->getData($sortAttribute) > $center) {
                $r--;
            }
            while ($array[$l]->getData($sortAttribute) < $center) {
                $l++;
            }
            if ($l <= $r) {
                list($array[$r], $array[$l]) = array($array[$l], $array[$r]);
                $l++;
                $r--;
            }
        } while ($l <= $r);

        if ($r > $left) {
            $this->_mySort($array, $left, $r, $sortAttribute);
        }

        if ($l < $right) {
            $this->_mySort($array, $l, $right, $sortAttribute);
        }
    }
}
