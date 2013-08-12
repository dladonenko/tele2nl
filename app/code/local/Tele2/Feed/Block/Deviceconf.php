<?php

class Tele2_Feed_Block_Deviceconf extends Mage_Core_Block_Template
{
    public $deviceAttributiesArray = array('name', 'price', 'usp', 'description', 'short_description');
    public $subscriptionAttributiesArray = array('name', 'subscription_id', 'priceplan', 'subscription_group', 'subtitle', 'usp', 'level');
    public $subscriptionBindingAttributiesArray = array('article_id', 'time', 'monthly_price_with_vat', 'monthly_price_without_vat');
    public $subscriptionAddonAttributiesArray = array('articleid', 'name', 'partnerid', 'monthly_price');
    public $subscriptionConfigAttributiesArray = array('article_id', 'name', 'priceplan', 'price_with_vat', 'price_without_vat');
    public $tsAttributiesArray = array();
    public $xmlElement;
    public $fmcgCategoryIds;
    protected $_productModel = null;
    protected  $_tele2VirtualStockModel = null;

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('feed_products_deviceconf_'
            . $this->getRequest()->getParam('store_id') . '_'
            . Mage::getModel('customer/session')->getId()
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $devices = $this->getDeviceCollection();
        $subscriptions = $this->getSubscriptionCollection();
        $this->fmcgCategoryIds = Mage::getModel('comviq_checkout/cart')->getFmcgCategoryIds();

        if ($devices->getSize() > 0) {
$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<rss version="2.0">
</rss>
XML;
            $this->xmlElement = new SimpleXMLElement($xmlstr);
            $channel = $this->xmlElement->addChild('channel');
            $channel->addChild("title", "Mobiltelefoner &amp; subscriptions");
            $channel->addChild("link", Mage::getUrl("*/*/*"));
            $channel->addChild("description", "List of enabled mobile telephones &amp; subscriptions");
            $mobileTelephonesChannel = $channel->addChild('mobile_telephones');
            if ($devices->getSize() > 0) {
                foreach ($devices as $device) {
                    $this->addDevice($device, $mobileTelephonesChannel);
                }
            }
            $subscriptionsChannel = $channel->addChild('subscriptions');
            if ($subscriptions->getSize() > 0) {
                foreach ($subscriptions as $subscription) {
                    $this->addSubscription($subscription, $subscriptionsChannel);
                }
            }
        }
        return $this->xmlElement->asXML();
    }

    /**
     * @param $product
     * @param $channel
     */
    protected function addDevice($product, $channel)
    {
        $item = $channel->addChild('item');
        $this->_addCData("brand", $this->_getBrand($product), $item);
        foreach ($this->deviceAttributiesArray as $attribute) {
            $this->_addCData($attribute, $product->getData($attribute), $item);
        }
        $ts = $item->addChild("ts");
        foreach ($this->tsAttributiesArray as $attribute) {
            $this->_addCData($attribute, $product->getData($attribute), $ts);
        }
        $this->_addCData("togo", $this->_isFmcg($product), $item);
        $associatedItems = $item->addChild("associated_products");
        $this->_addAssociatedProducts($product, $associatedItems);
        $subscriptions = $item->addChild("subscriptions");
        $this->_addSubscriptions($product, $subscriptions);
        $urls = $item->addChild("urls");
        $this->_addUrls($product, $urls);
    }

    /**
     * @param $product
     * @param $channel
     */
    protected function addSubscription($subscription, $channel)
    {
        $item = $channel->addChild('item');
        foreach ($this->subscriptionAttributiesArray as $attribute) {
            $this->_addCData($attribute, $subscription->getData($attribute), $item);
        }

        $type1Options = $subscription->getType1Options();
        $this->_addCData('type1', $type1Options[$subscription->getType1()]['label'], $item);

        $type2Options = $subscription->getType2Options();
        $this->_addCData('type2', $type2Options[$subscription->getType2()]['label'], $item);

        $downgradeOptions = $subscription->getDowngradeOptions();
        $this->_addCData('downgrade', $downgradeOptions[$subscription->getDowngrade()]['label'], $item);
        
        $bindingsChannel = $item->addChild('bindings');
        $subscriptionBindings = $subscription->getBindings();
        if ($subscriptionBindings) {
            foreach ($subscriptionBindings as $subscriptionBinding) {
                $this->_addBinding($subscriptionBinding, $bindingsChannel);
            }
        }
        $this->_addCData("standalone", $subscription->getStandalone(), $item);
        if (
            $subscription->getStandalone() && 
            $fakeProductId = $subscription->getFakeProductId()
        ) {
            $fakeProduct = Mage::getModel('catalog/product')->load($fakeProductId);
            if ($fakeProduct) {
                $subitem = $item->addChild('standalone_data');
                $this->addFakeProduct($fakeProduct, $subitem);
            }
        }
        $addonsChannel = $item->addChild('addons');
        $subscriptionAddons = $subscription->getAddonCollection();
        if ($subscriptionAddons) {
            foreach ($subscriptionAddons as $subscriptionAddon) {
                $this->_addAddon($subscriptionAddon, $addonsChannel);
            }
        }
        $configsChannel = $item->addChild('configs');
        $subscriptionConfigs = $subscription->getConfigsCollection();
        if ($subscriptionConfigs) {
            foreach ($subscriptionConfigs as $subscriptionConfig) {
                $this->_addConfig($subscriptionConfig, $configsChannel);
            }
        }
    }

    /**
     * @param $product
     * @param $channel
     */
    protected function addFakeProduct($product, $channel)
    {
        foreach ($this->deviceAttributiesArray as $attribute) {
            $this->_addCData($attribute, $product->getData($attribute), $channel);
        }
        $this->_addCData("togo", $this->_isFmcg($product), $channel);
        $urls = $channel->addChild("urls");
        $this->_addUrls($product, $urls);
        $images = $channel->addChild("images");
        $this->_addImages($product, $images);
    }

    /**
     * @param $subscriptionAddon
     * @param $channel
     */
    protected function _addAddon($subscriptionAddon, $channel)
    {
        $channelAddon = $channel->addChild('addon');
        foreach ($this->subscriptionAddonAttributiesArray as $attribute) {
            $this->_addCData($attribute, $subscriptionAddon->getData($attribute), $channelAddon);
        }
    }

    /**
     * @param $subscriptionBinding
     * @param $channel
     */
    protected function _addBinding($subscriptionBinding, $channel)
    {
        $channelBinding = $channel->addChild('binding');
        foreach ($this->subscriptionBindingAttributiesArray as $attribute) {
            $this->_addCData($attribute, $subscriptionBinding->getData($attribute), $channelBinding);
        }
    }

    /**
     * @param $subscriptionConfig
     * @param $channel
     */
    protected function _addConfig($subscriptionConfig, $channel)
    {
        $channelConfig = $channel->addChild('config');
        foreach ($this->subscriptionConfigAttributiesArray as $attribute) {
            $this->_addCData($attribute, $subscriptionConfig->getData($attribute), $channelConfig);
        }
    }

    /**
     * @param $product
     * @param $associatedItems
     */
    protected function _addAssociatedProducts($product, $associatedItems)
    {
        $associatedProducts = $product->getTypeInstance()->getUsedProducts();
        foreach ($associatedProducts as $associatedProduct) {
            $item = $associatedItems->addChild('item');
            $item->addChild('delivery_time', $this->_getExpectedDeliveryTime($associatedProduct));
            $item->addChild('color', $associatedProduct->getAttributeText('color'));
            $item->addChild('articleid', $associatedProduct->getArticleid());
            $item->addChild('logisticsArticleId', $associatedProduct->getPartnerid());

            $this->_addImages($associatedProduct, $item);

            $item->addChild('stock_level', $associatedProduct->getStockItem()->getQty());
            $item->addChild('inventory_status', ($associatedProduct->getIsInStock()?"In Stock":"Out of Stock"));
            
            $virtualStockCollection = $this->_getTele2VirtualStockModel()->getVirtualStock($associatedProduct);
            if (count($virtualStockCollection)) {
                $virtualStock = $item->addChild('virtual_stock');
                foreach ($virtualStockCollection as $virtualStockCollectionItem) {
                    $virtualStockItem = $virtualStock->addChild('item');
                    $virtualStockItem->addChild('level', $virtualStockCollectionItem->getLevel());
                    $virtualStockItem->addChild('expected_date', $virtualStockCollectionItem->getExpectedDate());
                    $virtualStockItem->addChild('amount', $virtualStockCollectionItem->getAmount());
                    $virtualStockItem->addChild('left', $virtualStockCollectionItem->getLeft());
                }
            }
            
        }
    }

    /**
     * @param $product
     * @param $channel
     */
    protected function _addImages($product, $channel)
    {
        if ($productGallery = $this->_getProductModel()->unsetData()->load($product->getId())->getMediaGalleryImages()) {
            $images = $channel->addChild('images');
            foreach ($productGallery as $_image) {
                $images->addChild('image', Mage::helper('catalog/image')->init($product, 'image', $_image->getFile()));
            }
        }
    }

    /**
     * @param $product
     * @param $subscriptions
     */
    public function _addSubscriptions($product, $subscriptions)
    {
        
        $productSubscriptions = $this->_getProductSubscriptions($product);
        foreach ($productSubscriptions as $productSubscriptionId => $productSubscription) {
            $subscription = $subscriptions->addChild('subscription');
            $this->_addCData('subscription_id', $productSubscriptionId, $subscription);
            $this->_addCData('name', $productSubscription['subscription']['name'], $subscription);
            $this->_addCData('description', $productSubscription['subscription']['description'], $subscription);
            $this->_addCData('usp', $productSubscription['subscription']['usp'], $subscription);
            $subscription->addChild('price_plan_code', $productSubscription['subscription']['priceplan']);
            foreach ($productSubscription['prices'] as $key => $price) {
                $binding = $subscription->addChild('binding');
                $binding->addChild('binding_period', $key);
                $binding->addChild('upfrontprice', $price['upfrontprice']);
                $binding->addChild('article_id', $price['article_id']);
                $binding->addChild('monthly_price_with_vat', $price['monthly_price_with_vat']);
            }
        }
    }

    /**
     * @param $product
     * @param $subscriptions
     */
    public function _addUrls($product, $urls)
    {
        $productSubscriptions = $this->_getProductSubscriptions($product);
        $productUrl = $product->getProductUrl();
        $urls->addChild('main', $productUrl);
        $i = 1;
        $pre = Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE_PRE;
        $post = Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE_POST;
        foreach ($productSubscriptions as $subscription) {
            $subscriptionType = (Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE == $subscription['subscription']['type1'] ? $pre : $post);
            $subscriptionId = $subscription['subscription']['subscription_id'];
            foreach ($subscription['prices'] as $key => $price) {
                $urls->addChild(
                    'url_'.$i++,
                    $productUrl
                        .'?subscriptionType='.$subscriptionType
                        .'&amp;s='. $subscriptionId
                        .'&amp;b='.$key
                );
            }
        }
    }

    protected function _addCData($nodename, $cdata_text, $item) 
    { 
        $node = $item->addChild($nodename);
        $node = dom_import_simplexml($node);
        $node->appendChild($node->ownerDocument->createCDATASection($cdata_text)); 
    }

    /**
     * @return $productsCollection
     */
    public function getDeviceCollection()
    {
        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load(Tele2_Install_Helper_Data::ATTR_SET_DEVICE, 'attribute_set_name')
            ->getAttributeSetId();
        $storeId = Mage::app()->getStore()->getStoreId();
        $productsCollection = Mage::getModel('catalog/product')->getCollection()
                ->addStoreFilter($storeId)
                ->addAttributeToSelect($this->deviceAttributiesArray)
                ->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
                ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $attributeGroupId = Mage::getModel('eav/entity_attribute_group')->getCollection()
                ->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
                ->addFieldToFilter('attribute_group_name', 'Specifications')
                ->load()
                ->getFirstItem()
                ->getData('attribute_group_id');
        $tsAttributies = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setAttributeGroupFilter($attributeGroupId)
                ->load();
        foreach ($tsAttributies as $tsAttribute) {
            $this->tsAttributiesArray[$tsAttribute->getAttributeId()] = $tsAttribute->getAttributeCode();
        }
        if (count($this->tsAttributiesArray)) {
            $productsCollection->addAttributeToSelect(array_values($this->tsAttributiesArray));
        }
        return $productsCollection;
    }

    /**
     * @return Collection
     */
    public function getSubscriptionCollection()
    {
        $productsCollection = Mage::getModel('tele2_subscription/mobile')->getCollection()
            ->join(
                array('as' => 'tele2_subscription/subscription'),
                'main_table.subscription_id = as.subscription_id',
                array('standalone', 'fake_product_id')
            )
            ->joinFlatTables();
        return $productsCollection;
    }

    protected function _getProductModel()
    {
        if (!$this->_productModel) {
            $this->_productModel = Mage::getModel('catalog/product');
        }
        return $this->_productModel;
    }

    protected function _getTele2VirtualStockModel()
    {
        if (!$this->_tele2VirtualStockModel) {
            $this->_tele2VirtualStockModel = Mage::getModel('tele2_cataloginventory/virtualstock');
        }
        return $this->_tele2VirtualStockModel;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array (subscriptions)
     */
    protected function _getProductSubscriptions(Mage_Catalog_Model_Product $product)
    {
        if (Mage::helper('tele2_catalog')->isDeviceOrDongle($product)) {
            $product = Mage::getModel('catalog/product')->load($product->getId());
            $return = array();
            foreach ($product->getOptions() as $option) {
                if ($option->getDefaultTitle() == Tele2_Install_Helper_Data::CUSTOM_OPTION_SUBSCRIPTIONS) {
                    foreach ($option->getValues() as $value) {
                        $subscription = Mage::helper('tele2_subscription')->getSubscriptionIdBySky($value->getSku());
                        if ($subscription) {
                            if (!isset($return[$subscription->getSubscriptionId()])) {
                                $return[$subscription->getSubscriptionId()]['subscription'] = $subscription->getData();
                            }
                            if (preg_match('%subscr-(\d+)-(\d+)%',  $value->getSku(), $foundSubscription)) {
                                if (is_array($foundSubscription)) {
                                    $bindingPeriod = $foundSubscription[2];
                                    $upfrontprice = $product->getPrice() + $value->getPrice();
                                    $return[$subscription->getSubscriptionId()]['prices'][$bindingPeriod]['upfrontprice'] = ($upfrontprice > 0) ? $upfrontprice : 0 ;
                                    $binding = $subscription->getBindingByPeriod($bindingPeriod);
                                    $return[$subscription->getSubscriptionId()]['prices'][$bindingPeriod]['article_id'] = $binding->getArticleId();
                                    $return[$subscription->getSubscriptionId()]['prices'][$bindingPeriod]['monthly_price_with_vat'] = $binding->getMonthlyPriceWithVat();
                                }
                            }
                        }
                    }
                }
            }
            return $return;
        }
        return array();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string category name
     */
    protected function _getBrand(Mage_Catalog_Model_Product $product)
    {
        $categoryIds = $product->getCategoryIds();
        $categoryCollection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addIdFilter($categoryIds)
        ;
        foreach ($categoryCollection as $category) {
            if ($category->getParentCategory()->getCode() == 'brands') {
                return $category->getName();
            }
        }
        return '';
    }

    /**
     * @param Mage_Catalog_Model_Product $associatedProduct
     * @return string (ex: 1-3 days)
     */
    protected function _getExpectedDeliveryTime(Mage_Catalog_Model_Product $associatedProduct)
    {
        $expectedWeeks = 0;
        $expectedDeliveryDays = $this->_getTele2VirtualStockModel()->getExpectedDeliveryTime($associatedProduct);
        if ($expectedDeliveryDays > 7) {
            $expectedWeeks = $this->_getTele2VirtualStockModel()->daysToWeeks($expectedDeliveryDays);
        }
        $msgDays = Mage::helper('comviq_catalog')->__('dagar');
        $msgWeeks = Mage::helper('comviq_catalog')->__('veckor');
        $weeksOffset = 1;
		$daysOffset = 2;
        if ($expectedWeeks > 0) {
            $result = $expectedWeeks . '-' . ($expectedWeeks + $weeksOffset) . ' ' . $msgWeeks;
        } else {
            if ($expectedDeliveryDays == 0) {
                $expectedDeliveryDays = 1;
            }
            $result = $expectedDeliveryDays . '-' . ($expectedDeliveryDays + $daysOffset) . ' ' . $msgDays;
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string category name
     */
    protected function _isFmcg(Mage_Catalog_Model_Product $product)
    {
        $productCategoriesIds = $product->getCategoryIds();

        if (array_intersect($this->fmcgCategoryIds, $productCategoriesIds)) {
            return 'true';
        }
        return 'false';
    }
}