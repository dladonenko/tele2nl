<?php
class Tele4G_Checkout_Model_Cart extends Mage_Checkout_Model_Cart
{

    private $maxPrePaidSubscription = 4;
    private $maxPrePaidDevice = 1;
    private $maxPostPaidSubscription = 2;

    private $countPrePaidDevice = 0;
    private $countPostPaidDevice = 0;
    private $countPrePaidStandalone = 0;
    private $countPrePaidStandaloneDevice = 0;
    private $countPostPaidStandalone = 0;
    private $countPostPaidStandaloneDevice = 0;
    private $countPostMBB = 0;
    private $countPreMBB = 0;

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function getParamsConfigurableAsSimple($product, $params)
    {
        $helperCommon = Mage::helper("tele4G_common/data");

        $attribute_id = $product->getResource()->getAttribute('color')->getAttributeId();

        $associated_product_id = $params['super_attribute'][$attribute_id];

        $value = Mage::getModel('catalog/product_option_value')->load(current($params['options']));
        $sku = $value->getSku();

        $associatedProductsSorted = $product->getTypeInstance()->getUsedProducts();
        foreach ($associatedProductsSorted as $associatedProduct) {
            if ($associatedProduct->getColor() == $associated_product_id) {

                $simple_product_id = $associatedProduct->getId();

                $product = Mage::getModel('catalog/product')->load($simple_product_id);
                $options = $product->getOptions();
                if ($options) {
                    foreach ($options as $option)
                    {
                        if ($option->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS) {
                            foreach ($option->getValues() as $value)
                            {
                                if ($value->getSku() == $sku) {
                                    $option_id = $value->getOptionId();
                                    $value_id = $value->getId();
                                    $params = array('uenc' => $params['uenc'],
                                        'product' => $product->getId(),
                                        'super_attribute' => array($attribute_id => $associated_product_id),
                                        'options' => array($option_id => $value_id)
                                    );
                                    return $params;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /*
     * This method save required and hiden addon in shoping cart for offer
     */

    public function saveRequiredHiddenAddon($product, $cart, $params)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        $aSubscrBT = array();
        $options = $product->getOptions();
        foreach ($options as $option) {
            if ($option->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS) {
                $optionSku = Mage::getModel('catalog/product_option_value')->load($params['options'][$option->getId()])->getSku();
                if (preg_match('%subscr-(\d+)-(\d+)%', $optionSku, $foundSBT)) {
                    if (is_array($foundSBT)) {
                        $subscription_id = $foundSBT[1];
                        $aSubscrBT['bt']    = $foundSBT[2];

                        $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);

//                        $relations = Mage::getModel('tele2_subscription/addonRelation')->getCollection()
//                            ->addFieldToSelect("addon_id")
//                            ->addFieldToFilter('subscription_id', $subscription_id);
                        $relations = Mage::getModel('tele2_subscription/addonRelation')->getCollection()
                                ->addFieldToFilter('stype_id', $_subscription->getType1())
                                ->addFieldToSelect("subscription_id")
                                ->addFieldToSelect("addon_id");

                        $aAddonIds = array();
                        foreach ($relations as $rel) {
                            if (is_null($rel->getSubscriptionId()) || $rel->getSubscriptionId() == $subscription_id) {
                                $aAddonIds[] = $rel->getAddonId();
                            }
                        }
                        $addonAttributeSetId = Mage::getModel('eav/entity_attribute_set')->load($helperCommon::ATTR_SET_ADDON, 'attribute_set_name')
                            ->getAttributeSetId();

                        $storeId = Mage::app()->getStore()->getStoreId();
                        $product_addon = Mage::getModel('catalog/product')
                            ->setStoreId($storeId)
                            ->getCollection()
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('price')
                            ->addAttributeToSelect('entity_id')
                            ->addFieldToFilter('entity_id', array('in' => $aAddonIds))
                            ->addFieldToFilter('attribute_set_id', $addonAttributeSetId)
                            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                        ;
                        $product_addon_ids = array();
                        foreach ($product_addon as $addon) {
                            $product_addon_ids[] = $addon->getId();
                        }
                        $this->addProductsByIds($product_addon_ids);
                    }
                }
            }
        }
        return false;
    }
    
    public function addProductsByIds($productIds)
    {
        $allAvailable = true;
        $allAdded     = true;

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $productId = (int) $productId;
                if (!$productId) {
                    continue;
                }
                $product = $this->_getProduct($productId);
                
                // hardcode for add to card addons with status invisible
                $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                $attributeSetModel->load($product->getAttributeSetId());
                $attributeSetName  = $attributeSetModel->getAttributeSetName();
                if($attributeSetName == 'addon'){
                    $visibleFlag = true;
                } else {
                    $visibleFlag = $product->isVisibleInCatalog();
                }
                
                if ($product->getId() && $visibleFlag) {                
                    try {
                        $this->getQuote()->addProduct($product);
                    } catch (Exception $e){
                        $allAdded = false;
                    }
                } else {
                    $allAvailable = false;
                }
            }

            if (!$allAvailable) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('Some of the requested products are unavailable.')
                );
            }
            if (!$allAdded) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('Some of the requested products are not available in the desired quantity.')
                );
            }
        }
        return $this;
    }

    public function getRulesForProduct($productId, $offerId)
    {
        $allVisibleItems = $this->getQuote()->getAllVisibleItems();
        foreach ($allVisibleItems as $visibleItem) {
            if ($visibleItem->getOfferId() == $offerId && $visibleItem->getProductId() == $productId) {
                $mainProductSku = $visibleItem->getSku();
            }
        }

        $subscription = Mage::helper('tele2_subscription')->getSubscriptionBySku($mainProductSku);

        $freeGifts = Mage::getModel('tele2_freeGift/freeGift')->getCollection();
        $bindingPeriodId = $subscription->getBindingByPeriod($subscription->getParamBindPeriod())
            ->getId();

        $freeGifts
            ->addFieldToFilter('condition_subscription_id', $subscription->getId())
            ->addFieldToFilter('condition_device_id', $productId)
            ->addFieldToFilter('coupon_code', null);
        ;

        $rule = $freeGifts->getFirstItem();
        $ruleBindings = explode(',', $rule->getConditionBindingPeriod());
        if (in_array($bindingPeriodId, $ruleBindings)) {
            return explode(',', $rule->getActionProductId());
        }

        return false;
    }

    public function getCouponRulesForProduct($item, $couponCode)
    {
        $mainProductSku = $item->getSku();

        $subscription = Mage::helper('tele2_subscription')->getSubscriptionBySku($mainProductSku);

        $freeGifts = Mage::getModel('tele2_freeGift/freeGift')->getCollection();
        $bindingPeriodId = $subscription->getBindingByPeriod($subscription->getParamBindPeriod())
            ->getId();

        $freeGifts
            ->addFieldToFilter('condition_subscription_id', $subscription->getId())
            ->addFieldToFilter('condition_device_id', $item->getProductId())
            ->addFieldToFilter('coupon_code', $couponCode);
        ;

        $rule = $freeGifts->getFirstItem();
        $ruleBindings = explode(',', $rule->getConditionBindingPeriod());
        if (in_array($bindingPeriodId, $ruleBindings)) {
            return explode(',', $rule->getActionProductId());
        }

        return false;
    }

    public function getCouponRules($couponCode)
    {
        $freeGifts = Mage::getModel('tele2_freeGift/freeGift')->getCollection()
            ->addFieldToFilter('coupon_code', $couponCode);

        return $freeGifts;
    }

    /*
     * return bool
     */
    public function isCanAddSubscriptionAndDevice($product, $params)
    {
        $quote   = $this->_getCart()->getQuote();
        $items = $quote->getAllVisibleItems();
        if (!count($items)) {
            return true;
        }

        if ($this->countSubscriptionsInCart($items)) {
            $helperCommon = Mage::helper("tele4G_common/data");
            $attributeSetName = $this->_getAttributeSetNameById($product->getAttributeSetId());

            if ($attributeSetName == $helperCommon::ATTR_SET_DEVICE) {
                $options = $product->getOptions();
                foreach ($options as $option) {
                    if ($option->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS) {
                        $optionSku = Mage::getModel('catalog/product_option_value')->load($params['options'][$option->getId()])->getSku();
                        if (preg_match('%subscr-(\d+)-(\d+)%', $optionSku, $foundSBT)) {
                            if (is_array($foundSBT)) {
                                $subscription_id = $foundSBT[1];
                                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                                if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                                    if (($this->countPrePaidDevice < $this->maxPrePaidDevice) && ($this->countPrePaidStandaloneDevice < $this->maxPrePaidSubscription)) {
                                        return true;
                                    } else {
                                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än en ({$this->countPrePaidDevice}) telefon på avbetalning via kontantkort på ett personnummer"));
                                        return false;
                                    }
                                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                                    if ($this->countPostPaidStandaloneDevice < $this->maxPostPaidSubscription) {
                                        return true;
                                    } else {
                                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än två ({$this->countPostPaidStandaloneDevice}) abonnemang på ett personnummer"));
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($product->getId(), 'fake_product_id');
                if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                    if (($this->countPrePaidStandalone < $this->maxPrePaidSubscription) && ($this->countPrePaidStandaloneDevice < $this->maxPrePaidSubscription)) {
                        return true;
                    } else {
                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än fyra ({$this->countPrePaidStandaloneDevice}) kontantkort registrerade på ett personnummer"));
                        return false;
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                    if ($this->countPostPaidStandaloneDevice < $this->maxPostPaidSubscription) {
                        return true;
                    } else {
                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än två ({$this->countPostPaidStandaloneDevice}) abonnemang på ett personnummer"));
                        return false;
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE) {
                    if (($this->countPrePaidStandalone < $this->maxPrePaidSubscription) && ($this->countPrePaidStandaloneDevice < $this->maxPrePaidSubscription)) {
                        return true;
                    } else {
                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än fyra ({$this->countPrePaidStandaloneDevice}) kontantkort registrerade på ett personnummer"));
                        return false;
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST ) {
                    if ($this->countPostPaidStandaloneDevice < $this->maxPostPaidSubscription) {
                        return true;
                    } else {
                        Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__("Du kan inte ha fler än två ({$this->countPostPaidStandaloneDevice}) abonnemang på ett personnummer"));
                        return false;
                    }
                }
            }else {
                return true;
            }
        }
        return false;
    }
    
    private function countSubscriptionsInCart($items)
    {
        if ($items) {
            $helperCommon = Mage::helper("tele4G_common/data");
            foreach($items as $item)
            {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if ($attributeSetName == $helperCommon::ATTR_SET_DEVICE && $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    if (preg_match('%subscr-(\d+)-(\d+)%',  $item->getSku(), $foundSBT)) {
                        if (is_array($foundSBT)) {
                            $_subscriptionId = $foundSBT[1];
                            $_subscription = Mage::getModel('tele2_subscription/mobile')->load($_subscriptionId);
                            if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                                $this->countPrePaidStandaloneDevice++;
                                $this->countPrePaidDevice++;
                            } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                                $this->countPostPaidStandaloneDevice++;
                                $this->countPostPaidDevice++;
                            }
                        }
                    }
                } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                    $_subscription = Mage::getModel('tele2_subscription/mobile')->load($item->getProduct()->getId(), 'fake_product_id');
                    if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                        $this->countPrePaidStandaloneDevice++;
                        $this->countPrePaidStandalone++;
                    } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                        $this->countPostPaidStandaloneDevice++;
                        $this->countPostPaidStandalone++;
                    } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE) {
                        $this->countPrePaidStandaloneDevice++;
                        $this->countPrePaidStandalone++;
                    } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST) {
                        $this->countPostPaidStandaloneDevice++;
                        $this->countPostPaidStandalone++;
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function showAddToCartButton($product)
    {
        return true;

        if ($product->getAttributeText('subscription_type2') == 'mbb') {
            return true;
        }
            
        $quote   = $this->_getCart()->getQuote();
        $items = $quote->getAllVisibleItems();
        if (!count($items)) {
            return true;
        }

        $helperCommon = Mage::helper("tele4G_common/data");
        $attributeSetName = $this->_getAttributeSetNameById($product->getAttributeSetId());

        if ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
            if ($this->countSubscriptionsInCart($items)) {
                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($product->getId(), 'fake_product_id');
                if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                    $this->countPrePaidStandalone++;
                    if (($this->countPrePaidStandalone <= 1) && ($this->countPrePaidDevice < 5)) {
                        return true;
                    }
                } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                    $this->countPostPaidStandalone++;
                    if (($this->countPostPaidStandalone <= 2) && ($this->countPostPaidDevice < 2)) {
                        return true;
                    }
                }
            }
        } elseif (
            $attributeSetName == $helperCommon::ATTR_SET_DEVICE ||
            $attributeSetName == $helperCommon::ATTR_SET_DONGLE
        ) {
            return true;
        }
        return false;
    }

    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }


    /**
     * Check: has quote or product (adding) a Subscription?
     *
     * @param $product
     * @return bool
     */
    public function hasQuoteSubscription(Mage_Catalog_Model_Product $product)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        $attributeSetName = $this->_getAttributeSetNameById($product->getAttributeSetId());
        if (($attributeSetName == $helperCommon::ATTR_SET_DEVICE && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) ||
            $attributeSetName == $helperCommon::ATTR_SET_DONGLE) {
            // @todo: maybe, it will be rewrite
            if ($product->getTypeInstance(true)->hasOptions($product)) {
                foreach ($product->getOptions() as $productCustomOption) {
                    if ($productCustomOption->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS &&
                        is_array($productCustomOption->getValues())) {
                        return true;
                    }
                }
            }
        } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
            return true;
        } else {
            $quoteItems = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
            foreach ($quoteItems as $quoteItem) {
                $attributeSetName = $this->_getAttributeSetNameById($quoteItem->getProduct()->getAttributeSetId());
                if (($attributeSetName == $helperCommon::ATTR_SET_DEVICE && $quoteItem->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) ||
                    $attributeSetName == $helperCommon::ATTR_SET_DONGLE ||
                    $attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                    return true;
                }
            }
        }
        Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__("Error! Your cart does not have a Subscription!"));
        return false;
    }

    public function hasPostSubscription($order = null)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if ($order instanceof Mage_Sales_Model_Order) {
            $items = $order->getItemsCollection();
        } else {
            $items = $this->_getCart()->getQuote()->getAllItems();
        }

        if (count($items)) {
            foreach ($items as $item) {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if (
                    (
                        $attributeSetName == $helperCommon::ATTR_SET_DEVICE &&
                        $item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                    ) || 
                    $attributeSetName == $helperCommon::ATTR_SET_DONGLE
                ) {
                    if (preg_match('%subscr-(\d+)-(\d+)%', $item->getSku(), $subscription)) {
                        $subscription_id = $subscription[1];
                        $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                        if (
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST ||
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST
                        ) {
                            return true;
                        }
                    }
                } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                    $_subscription = Mage::getModel('tele2_subscription/mobile')->getSubscriptionByProductId($item->getProduct()->getId());
                    if (
                        $_subscription->getId() && 
                        (
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST ||
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST
                        )
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function hasPreSubscription($order = null)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if ($order instanceof Mage_Sales_Model_Order) {
            $items = $order->getItemsCollection();
        } else {
            $items = $this->_getCart()->getQuote()->getAllItems();
        }

        if (count($items)) {
            foreach ($items as $item) {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if (
                    (
                        $attributeSetName == $helperCommon::ATTR_SET_DEVICE &&
                        $item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                    ) || 
                    $attributeSetName == $helperCommon::ATTR_SET_DONGLE
                ) {
                    if (preg_match('%subscr-(\d+)-(\d+)%', $item->getSku(), $subscription)) {
                        $subscription_id = $subscription[1];
                        $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                        if (
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE ||
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE
                        ) {
                            return true;
                        }
                    }
                } elseif ($attributeSetName == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                    $_subscription = Mage::getModel('tele2_subscription/mobile')->getSubscriptionByProductId($item->getProduct()->getId());
                    if (
                        $_subscription->getId() && 
                        (
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE ||
                            $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE
                        )
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function hasSfaSubscription($order = null)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $items = $order->getItemsCollection();
        } else {
            $items = $this->_getCart()->getQuote()->getAllItems();
        }
        $helperCommon = Mage::helper("tele4G_common/data");

        if (count($items)) {
            foreach ($items as $item) {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if (
                    (
                        $attributeSetName == $helperCommon::ATTR_SET_DEVICE &&
                        $item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                    ) ||
                    $attributeSetName == $helperCommon::ATTR_SET_DONGLE
                ) {
                    if (preg_match('%subscr-(\d+)-(\d+)%', $item->getSku(), $subscription)) {
                        $subscription_id = $subscription[1];
                        $bp = $subscription[2];
                        $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                        if (
                            ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) &&
                            $bp > 0
                        ) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function hasInsurance($order = null)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $items = $order->getItemsCollection();
        } else {
            $items = $this->_getCart()->getQuote()->getAllItems();
        }
        $helperCommon = Mage::helper("tele4G_common/data");

        if (count($items)) {
            foreach ($items as $item) {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if ($attributeSetName == $helperCommon::ATTR_SET_INSURANCE) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isOrderHasAddon($order)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        $orderItems = $order->getItemsCollection();
        if (count($orderItems)) {
            foreach ($orderItems as $orderItem) {
                $attributeSetName = $this->_getAttributeSetNameById($orderItem->getProduct()->getAttributeSetId());
                if ($attributeSetName == $helperCommon::ATTR_SET_ADDON) {
                    return true;
                }
            }
        }
        return false;
    }
    
    
    
    
    public function getSubscriptionBindPeriod($order = null)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        if ($order instanceof Mage_Sales_Model_Order) {
            $items = $order->getItemsCollection();
        } else {
            $items = $this->_getCart()->getQuote()->getAllItems();
        }

        if (count($items)) {
            foreach ($items as $item) {
                $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                if (
                    (
                        $attributeSetName == $helperCommon::ATTR_SET_DEVICE &&
                        $item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                    ) || 
                    $attributeSetName == $helperCommon::ATTR_SET_DONGLE
                ) {
                    if (preg_match('%subscr-(\d+)-(\d+)%', $item->getSku(), $subscription)) {
                        $subscription_period = $subscription[2];
                        return $subscription_period;
                    }
                }
            }
        }
        return false;
    }
    
    
    
    /*
     *  IF order contains one or more of following AND customer < 18 years:
     *  a postpaid subscription
     *  a subscription with binding period
     *  cart has a cost higher than 0
     * return boolean
     */
    public function ssnValidateByAgePhoneType()
    {
        $offerData = Mage::getSingleton('checkout/cart')->getQuote()->getOfferData();
        if(isset($offerData)){
            $offerData = unserialize($offerData);
            foreach ($offerData as $offer){
                if(isset($offer['number']) and isset($offer['type']) and ($offer['type'] == 'PORT') ){
                    return true;
                }
            }        
        }
        return false;
    }
    
    public function ssnValidateByAge()
    {
        $totalPrice = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
        $isPost = $this->hasPostSubscription();
        if(($totalPrice > 0) or $isPost or ($this->getSubscriptionBindPeriod() > 0)){
            return true;
        }
        return false;
    }
    
    public function checkAddAddonToCart($addons)
    {
        $helperCommon = Mage::helper("tele4G_common/data");
        
        $addonsLoad = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getStoreId())
            ->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $addons))
            ->addAttributeToSelect($helperCommon::ATTR_GROUP_ADDON)
            ->load();
        foreach ($addonsLoad as $addonLoad) {
            $groupAddon = $addonLoad->getData($helperCommon::ATTR_GROUP_ADDON);
            $groupAddonsLoad[$groupAddon] = $addonLoad->getId();
            
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getItemsCollection()->load();
        $quoteIds = array();
        foreach ($items as $item) {
            if ($this->getCheckoutSession()->getOfferId() == $item->getOfferId()) {
                $quoteIds[$item->getId()] = $item->getProductId();
            }
        }
        $groupAddonsLoad = array_diff($groupAddonsLoad, $quoteIds);
        $addonGroupIds = array_keys($groupAddonsLoad);
        if ($addonGroupIds) {
            $addonFromGroupAddon = Mage::getModel('catalog/product')->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('entity_id', array('in' => $quoteIds))
                ->addAttributeToFilter($helperCommon::ATTR_GROUP_ADDON,  array('in' => $addonGroupIds));
            $productIds = array_flip($quoteIds);
            foreach ($addonFromGroupAddon as $addon) {
                $quote->removeItem($productIds[$addon->getId()]);
            }
        }
        
        return $groupAddonsLoad;
    }
    
    /**
     * checkAddInsuranceToCart
     * 
     * @param type $relatedInsurances
     * @return type
     */
    public function checkAddInsuranceToCart($relatedInsurances)
    {
        $relatedInsurances = array_slice($relatedInsurances, 0, 1);
        $insuranceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
                ->load(Tele4G_Common_Helper_Data::ATTR_SET_INSURANCE, 'attribute_set_name')
                ->getAttributeSetId();
        
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getItemsCollection()->load();
        foreach ($items as $item) {
            if ($this->getCheckoutSession()->getOfferId() == $item->getOfferId()
                    && $item->getProduct()->getAttributeSetId() == $insuranceAttributeSetId) {
                $quote->removeItem($item->getId());
            }
        }
        return $relatedInsurances;
    }

    public function getIsFmcgOnly()
    {
        $items = $this->getQuote()->getAllVisibleItems();
        if (!count($items)) {
            return false;
        }
        if (!$this->hasPostSubscription()) {
            return false;
        }
        foreach ($items as $item) {
            if (!$this->isQuoteItemFMCG($item)) {
                return false;
            }
        }
        return true;
    }
    
    public function hasQuoteFmcg($flagItem = false, $order = false)
    {
        if ($order) {
            $items = $order->getAllVisibleItems();
        } else {
            $items = $this->getQuote()->getAllVisibleItems();
        }
        if (!count($items)) {
            return false;
        }
        if (!$this->hasPostSubscription($order)) {
            return false;
        }
        foreach ($items as $item) {
            if ($this->isQuoteItemFMCG($item)) {
                if ($flagItem) {
                    return $item;
                } else {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function isQuoteItemFMCG($quoteItem = null)
    {
        if (!$quoteItem) {
            return false;
        }
        $fmcgFlag = false;
        $catalogHelper = Mage::helper('tele2_catalog');
        if ($catalogHelper->isDeviceOrDongle($quoteItem->getProduct())) {
            $subscription = Mage::helper('tele2_subscription')->getSubscriptionBySku($quoteItem->getSku());
            if ($subscription->getParamBindPeriod() == Tele4G_Togo_Model_Togo::BIND_PERIOD_24) {
                $fmcgFlag = true;
            }
        } elseif ($catalogHelper->isSubscription($quoteItem->getProduct())) {
            $fmcgFlag = true;
        } elseif ($catalogHelper->isAddon($quoteItem->getProduct())) {
            $isAllowAddons = Mage::getStoreConfig('carriers/togo/allow_addons');
            if ($isAllowAddons) {
                $fmcgFlag = true;
            }
        } elseif ($catalogHelper->isInsurance($quoteItem->getProduct())) {
            $isAllowInsurance = Mage::getStoreConfig('carriers/togo/allow_insurance');
            if ($isAllowInsurance) {
                $fmcgFlag = true;
            }
        }
        $fmcgCategoriesIds = $this->getFmcgCategoryIds();
        $itemCategoriesIds = $quoteItem->getProduct()->getCategoryIds();
        if (count(array_intersect($fmcgCategoriesIds, $itemCategoriesIds)) && $fmcgFlag) {
            return true;
        }
    }
    
    public function getFmcgCategoryIds()
    {
        $fmcgCategoriesIds = array();
        $fmcgCategories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('code', array('in' => array('fmcg','fmcg_subscription')))
            ->load();
        foreach ($fmcgCategories as $fmcgCategory) {
            $fmcgCategoriesIds[] = $fmcgCategory->getId();
        }
        return $fmcgCategoriesIds;
    }
}