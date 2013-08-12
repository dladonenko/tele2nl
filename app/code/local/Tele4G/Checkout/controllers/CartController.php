<?php

/**
 * Shopping cart controller
 */

require ROOT_PATH.'app/code/core/Mage/Checkout/controllers/CartController.php';
class Tele4G_Checkout_CartController extends Mage_Checkout_CartController
{
    protected $params = array();
    protected $_offerData = array();
    protected $_product;
    protected $_offerId;
    protected $_activationType;
    protected $_simType;
    protected $_activationNumber;

    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
             return $product;
        }
        return false;
    }

    public function indexAction()
    {
        if (count($this->_getCart()->getItems()) || $this->_getCheckout()->getShowDowngradePopup()) {
            parent::indexAction();
        } else {
            $this->_redirect('/');
            return;
        }
    }

    public function addAction()
    {
        $cart   = $this->_getCart();
        $this->params = $this->getRequest()->getParams();
        $offer = Mage::getModel('tele4G_checkout/offer');
        $catalogHelper = Mage::helper('tele2_catalog');

        try {
            if (isset($this->params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $this->params['qty'] = $filter->filter($this->params['qty']);
            }

            $this->_product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$this->_product) {
                $this->_goBack();
                return;
            }

            $tele4GCart = Mage::getModel('tele4G_checkout/cart');
            $isCanAdd = $tele4GCart->isCanAddSubscriptionAndDevice($this->_product, $this->params);
            $hasQuoteSubscription = $tele4GCart->hasQuoteSubscription($this->_product);

            if($isCanAdd && $hasQuoteSubscription) {
                $cartAdded = $cart->addProduct($this->_product, $this->params);
            } else {
                $this->_goBack();
                return;
            }

            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            if (!$offer->initOffer($this->_product)) {
                $this->_goBack();
                return;
            }

            if ($catalogHelper->isDeviceOrDongle($this->_product)) {
                $offerId = $this->_getCheckout()->getOfferId();

                $giftIds = $tele4GCart->getRulesForProduct($this->_product->getId(), $offerId);

                if ($giftIds && is_array($giftIds)) {
                    foreach ($giftIds as $giftId) {
                        $gift = Mage::getModel('catalog/product')->load($giftId);
                        if ($gift) {
                            $cart->addProductsByIds(array($giftId));
                            $items = $this->_getQuote()->getAllVisibleItems();
                            $giftItem = array_pop($items);

                            if (!$giftItem->getOfferId()) {
                                $giftItem->setOfferId($offerId);
                                $offer->saveAdditionalInfoForServices($gift, $giftItem);
                            }
                        }
                    }
                }
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);
            
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                $this->_getSession()->setOfferParamsAfterCart($this->params);
                if ($this->getRequest()->getParam('usetogo')) {
                    $this->_redirect('checkout/cart');
                } else {
                    $this->_redirect('checkout/cart/item');
                }
            }

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $this->_product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    public function addAddonAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();

        try {

            $related = $this->getRequest()->getParam('related_products');
            $relatedInsurances = $this->getRequest()->getParam('related_insurances');
            if (empty($related) && empty($params['addon']) && empty($relatedInsurances)) {
                $this->_goBack();
                return $this;
            }

            if (isset($params['addon']) && count($params['addon'])) {
                $addons = Mage::getModel('tele4G_checkout/cart')->checkAddAddonToCart($params['addon']);
                foreach ($addons as $addon)
                {
                    $cart->addProduct($addon);
                }
            }

            if (!empty($related)) {
                $cart->addProductsByIds($related);
            }
            if (!empty($relatedInsurances)) {
                $relatedInsurances = Mage::getModel('tele4G_checkout/cart')->checkAddInsuranceToCart($relatedInsurances);
                $cart->addProductsByIds($relatedInsurances);
            }

            $items = $this->_getQuote()->getAllVisibleItems();
            $offer = Mage::getModel('tele4G_checkout/offer');

            foreach($items as $item)
            {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if (!$item->getOfferId()) {
                    $item->setOfferId($this->_getCheckout()->getOfferId());
                    
                    $offer->saveAdditionalInfoForServices($product, $item);
                }
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                $this->_goBack();
            }

        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $helperCommon = Mage::helper("tele4G_common/data");

        if ($id) {
            try {

                $current_item = $this->_getCart()->getQuote()->getItemById($id);
                if ($current_item) {
                    $product = Mage::getModel('catalog/product')->load($current_item->getProductId());
                    $attribute_name = $this->_getAttributeSetNameById($product->getAttributeSetId());

                    if ($attribute_name == $helperCommon::ATTR_SET_DEVICE || $attribute_name == $helperCommon::ATTR_SET_SUBSCRIPTION) {
                        $items = $this->_getCart()->getItems();

                        foreach($items as $item)
                        {
                            if ($item->getOfferId() == $current_item->getOfferId()){
                                $this->_getCart()->removeItem($item->getId())->save();
                                $this->_deleteOfferData($item->getOfferId());
                            }
                        }
                    } else {
                        $this->_getCart()->removeItem($current_item->getId())->save();
                    }
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
    
    public function delete_offerAction()
    {
        $offer_id = $this->getRequest()->getParam('offer_id');
        $items = $this->_getCart()->getItems();
        try {
            foreach($items as $item)
            {
                if ($item->getOfferId() == $offer_id) {
                    $this->_getCart()->removeItem($item->getId())->save();
                    $this->_deleteOfferData($offer_id);
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot remove the item.'));
            Mage::logException($e);
        }

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
    
    /**
     * Delete Offer Data from Quote on delete item from cart
     * 
     * @param int $offerId
     */
    private function _deleteOfferData($offerId = '')
    {
        if ($offerId) {
            $offerData = unserialize($this->_getCart()->getQuote()->getOfferData());
            if (isset($offerData[$offerId])) {
                unset($offerData[$offerId]);
                $this->_getCart()->getQuote()->setOfferData(serialize($offerData))
                    ->save();
            }
        }
    }

    /*
     * This method show page with addons and accessories for device/subscription
     */
    public function itemAction()
    {
        $block = $this->getLayout()->createBlock('tele4G_checkout/item');

        $hasAddons = $block->getCompatibleAddon();
        $hasAddons = ($hasAddons && $hasAddons->getSize()) ? true : false;

        $hasAccessories = $block->getCompatibleAccessories();
        $hasAccessories = ($hasAccessories && $hasAccessories->getSize()) ? true : false;

        $offerParams = $this->_getSession()->getOfferParamsAfterCart();

        if ($offerParams && ($hasAccessories || $hasAddons)) {
            $this->loadLayout()->renderLayout();
        } else {
            $this->_goBack();
        }
    }
    
    public function clearCartAction()
    {
        $this->_emptyShoppingCart();
        $this->_goBack();
    }
    
    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }

    /**
     * Initialize coupon
     */
    public function giftCouponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('gift_coupon_code');

        $session = $this->_getSession();
        if ($session->getCoupon() == $couponCode) {
            $this->_goBack();
            return;
        }

        $offer = Mage::getModel('tele4G_checkout/offer');
        $offerId = $this->_getCheckout()->getOfferId();

        try {
            if ($couponCode && $offerId) {
                $cart   = $this->_getCart();
                $tele4GCart = Mage::getModel('tele4G_checkout/cart');

                $rules = $tele4GCart->getCouponRules($couponCode);
                $canSave = false;

                if (count($rules)) {
                    $giftIds = array();
                    foreach ($rules as $rule) {
                        if (!$rule->getConditionDeviceId() && !$rule->getConditionSubscriptionId()) {//coupon without other rules
                            $giftIds = explode(',', $rule->getActionProductId());
                            if (count($giftIds)) {
                                //get offer to connect gift to
                                $offerIdForGift = null;
                                $mainOfferItem = null;
                                $cartItems = $this->_getQuote()->getAllVisibleItems();
                                foreach ($cartItems as $cartItem) {
                                    if (strpos($cartItem->getSku(), '-subscr-') != false) {//main product of the offer
                                        if (!$offerIdForGift) {
                                            $offerIdForGift = $cartItem->getOfferId();
                                        }

                                        $subscription = Mage::helper('tele2_subscription')->getSubscriptionBySku($cartItem->getSku());
                                        if ($subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                                            $offerIdForGift = $cartItem->getOfferId();
                                            break;
                                        }
                                    } else {//can be fake product for a subscription
                                        if ($cartItem->getProduct()->getAttributeText('subscription_type') == 'post') {
                                            $offerIdForGift = $cartItem->getOfferId();
                                            break;
                                        }
                                    }
                                }

                                if (!$offerIdForGift) {
                                    $offerIdForGift = $offerId;
                                }

                                foreach ($giftIds as $giftId) {
                                    $gift = Mage::getModel('catalog/product')->load($giftId);
                                    if ($gift) {
                                        $cart->addProductsByIds(array($giftId));
                                        $items = $this->_getQuote()->getAllVisibleItems();
                                        $giftItem = array_pop($items);

                                        if (!$giftItem->getOfferId()) {
                                            $giftItem->setOfferId($offerIdForGift);
                                            $offer->saveAdditionalInfoForServices($gift, $giftItem);
                                            $canSave = true;
                                        }
                                    }
                                }

                                if ($canSave) {
                                    $cart->save();
                                    $session->setCoupon($couponCode);
                                }

                                $this->_goBack();
                                return;
                            }
                        } else {//coupon with rules for subscriptions/bindings/devices
                            $cartItems = $this->_getQuote()->getAllVisibleItems();
                            foreach ($cartItems as $cartItem) {
                                if (strpos($cartItem->getSku(), '-subscr-') != false && $cartItem->getOfferId() == $offerId) {//main product of an offer
                                    $offerGiftIds = $tele4GCart->getCouponRulesForProduct($cartItem, $couponCode);
                                    if ($offerGiftIds) {
                                        $giftIds = array_merge($giftIds, $offerGiftIds);
                                    }
                                    foreach ($giftIds as $giftId) {
                                        $gift = Mage::getModel('catalog/product')->load($giftId);
                                        if ($gift) {
                                            $cart->addProductsByIds(array($giftId));
                                            $items = $this->_getQuote()->getAllVisibleItems();
                                            $giftItem = array_pop($items);

                                            if (!$giftItem->getOfferId()) {
                                                $giftItem->setOfferId($offerId);
                                                $offer->saveAdditionalInfoForServices($gift, $giftItem);
                                                $canSave = true;
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                            if ($canSave) {
                                $cart->save();
                                $session->setCoupon($couponCode);
                                $this->_goBack();
                                return;
                            }
                        }
                    }
                } elseif (preg_match("/^07/", $couponCode)) {
                    $friendPhone = preg_replace("/[^0-9]/", "", $couponCode);
                    if (!empty($friendPhone)) {
                        $quote = $this->_getQuote();
                        $offerData = unserialize($quote->getOfferData());
                        $offerData['friend_phone'] = "FR:".$friendPhone;
                        $quote->setOfferData(serialize($offerData));
                        $quote->save();
                        $message = Mage::helper("tele4G_checkout")->__('Campaign code %s was saved.', $friendPhone);
                        $session->addNotice($message);
                        $this->_goBack();
                        return;
                    }
                } else {
                    $this->_redirect('*/*/couponpost', array('coupon_code'=>$couponCode));
                    return;
                }
            } else {
                throw new Exception();
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the gift coupon code.'));
            Mage::logException($e);
        }

        $this->_goBack();
        return;
    }
}