<?php

class Tele4G_Checkout_Model_Offer extends Mage_Checkout_Model_Cart 
{
    protected $_offerData = array();
    protected $_product;

    const ACTIVATION_TYPE_NEW = 'NEW';
    const ACTIVATION_TYPE_PROLONG = 'PROLONG';
    const ACTIVATION_TYPE_PORT = 'PORT';
    const ACTIVATION_TYPE_CONVERT = 'CONVERT';

    public function getOffersAttrSetName()
    {
        return array(
            Tele2_Install_Helper_Data::ATTR_SET_DEVICE,
            Tele2_Install_Helper_Data::ATTR_SET_DONGLE,
            Tele2_Install_Helper_Data::ATTR_SET_SUBSCRIPTION,
        );
    }

    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    protected function _getAttributeSetNameById($id = null)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($id)->getAttributeSetName();
    }
    
    public function getLastQuoteItem()
    {
        $items = $this->getQuote()->getAllVisibleItems();
        return end($items);
    }
    
    public function newOfferId($item)
    {
        $incrementId = Mage::getSingleton('eav/config')
                ->getEntityType('quote_offer')
                ->fetchNewIncrementId();
        
        $this->setOfferId($incrementId);
        $this->_getCheckout()->setOfferId($incrementId);
        $item->setOfferId($incrementId);
    }

    public function setActivationTypeNew($_subscription)
    {
        $this->setActivationType(self::ACTIVATION_TYPE_NEW);
        if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
            if ($newnumber = Mage::app()->getRequest()->getParam('newnumber')) {
                $this->setActivationNumber($newnumber);
                Mage::helper('tele4G_sS4Integration')->removeChosenNumber($newnumber);
            } else {
                return false;
            }
        }
        return true;
    }

    public function setActivationTypeExist()
    {
        if ($exist_number = $this->_getCheckout()->getActivationExistNumber()) {
            $this->setActivationType($this->_getCheckout()->getActivationExistType());
            $this->setActivationNumber($exist_number);

            $this->_getCheckout()->unsActivationExistNumber();
            $this->_getCheckout()->unsActivationExistType();
        } else {
            return false;
        }
        return true;
    }

    public function saveOfferData($quoteItem)
    {
        if ($radioActivationType = Mage::app()->getRequest()->getParam('radioActivationType')) {
            if($this->_getAttributeSetNameById($quoteItem->getProduct()->getAttributeSetId()) == Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION) {
                $_subscription = Mage::getModel('tele2_subscription/mobile')->getSubscriptionByProductId($this->_product->getId());
            } else {
                $_subscription = Mage::helper('tele2_subscription/data')->getSubscriptionBySku($quoteItem->getSku());
            }
            if ($radioActivationType == "new") {
                if (!$this->setActivationTypeNew($_subscription)) {
                    $this->offerDataError('NO_NEW_NUMBER');
                    return false;
                }
            } elseif ($radioActivationType == "exist") {
                if (!$this->setActivationTypeExist()) {
                    $this->offerDataError('NO_EXIST_NUMBER');
                    return false;
                }
            }
        } else {
            $this->offerDataError('NO_ACTIVATION_TYPE');
            return false;
        }

        $this->_offerData = unserialize($quoteItem->getQuote()->getOfferData());
        $radioSimNotNeed = Mage::app()->getRequest()->getParam('radioSimNotNeed');
        if ($radioSimNotNeed && in_array(strtolower($this->getActivationType()), array('prolong', 'none'))) {
            $this->_offerData[$this->getOfferId()]['sim_type'] = null;
        } else {
            if ($this->_getAttributeSetNameById($this->_product->getAttributeSetId()) == Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION) {

                if (
                    $simType = Mage::helper('tele4G_catalog/simonly')->getSimType($quoteItem->getSku())
                ) {
                    /** SIM-only > Multiple SIM-types */
                    $this->setSimType($simType);
                } else {
                    /** non SIM-only */
                    $this->setSimType(Mage::app()->getRequest()->getParam('sim_type'));
                }

                $this->_offerData[$this->getOfferId()]['sim_type'] = $this->getSimType();
            } else {
                $this->_offerData[$this->getOfferId()]['sim_type'] = $this->_product->getAttributeText('sim_type');
            }
            if (empty($this->_offerData[$this->getOfferId()]['sim_type'])) {
                $this->offerDataError('NO_SIM_TYPE');
                return false;
            }
        }
        if ($this->getActivationType()) {
            $this->_offerData[$this->getOfferId()]['type'] = $this->getActivationType();
        }
        if ($this->getActivationNumber()) {
            if ($this->isExistingNumberInOfferData($this->getActivationNumber())) {
                $this->offerDataError('NUMBER_EXIST');
                return false;
            } else {
                $this->_offerData[$this->getOfferId()]['number'] = $this->getActivationNumber();
            }
        }
        
        $quoteItem->getQuote()->setOfferData(serialize($this->_offerData));
        $quoteItem->getQuote()->setAssistantData($this->_getAssistant());
        return true;
    }

    public function saveAdditionalInfoForMainProduct($quoteItem)
    {
        $product = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());
        $this->setProductDataToQuoteItem($quoteItem, $product);
        $parentItem = $quoteItem->getParentItem();
        if (!empty($parentItem)) {
            $this->setProductDataToQuoteItem($parentItem, $product);
        }
    }
    
    /**
     * saveAdditionalInfoForServices
     * 
     * @param type $_product
     * @param type $quoteItem
     */
    public function saveAdditionalInfoForServices($_product, $quoteItem)
    {
        $_additionalData = $quoteItem->getAdditionalData();
        $additionalData = unserialize($_additionalData);
        if ($this->_getAttributeSetNameById($_product->getAttributeSetId()) == Tele4G_Common_Helper_Data::ATTR_SET_ADDON) {
            $additionalData['monthly_price'] = $_product->getMonthlyPrice();
            $additionalData['monthly_price_without_vat'] = $_product->getMonthlyPriceWithoutVat();
        }
        if ($this->_getAttributeSetNameById($_product->getAttributeSetId()) == Tele4G_Common_Helper_Data::ATTR_SET_INSURANCE) {
            $additionalData['product_offer_article_id'] = $this->getProductOfferFromQuote()->getArticleId();
            $additionalData['product_code'] = $_product->getProductCode();
            $additionalData['insured_months'] = $_product->getInsuredMonths();
        }
        $this->_setProductDataToQuoteItem($quoteItem, $_product, $additionalData);
    }
    
    /**
     * _setProductDataToQuoteItem
     * 
     * @param type $quoteItem
     * @param type $_product
     * @param type $additionalData
     */
    protected function _setProductDataToQuoteItem($quoteItem, $_product, $additionalData)
    {
        $this->_setArticalId($quoteItem, $_product);
        $quoteItem->setPartnerId($_product->getPartnerid());
        $quoteItem->setMake($_product->getMake());
        $quoteItem->setDescription($_product->getDescription());
        
        $additionalData['price_without_vat'] = $_product->getPricewithoutvat();
        $additionalData['price_with_vat'] = $_product->getPricewithvat();
        $additionalDataSerialized = serialize($additionalData);
        $quoteItem->setAdditionalData($additionalDataSerialized);
    }

    protected function _getAssistant()
    {
        $cookieName = 'COP';
        $cookieValue = Mage::getModel('core/cookie')->get($cookieName);
        if ($cookieValue) {
            return Mage::helper('tele4G_sS4Integration')->decryptCookie($cookieValue);
        }

        return null;
    }

    public function initOffer($product)
    {
        $this->_product = $product;

        $item = $this->getLastQuoteItem();
        $this->newOfferId($item);

        $parentItem = $item->getParentItem();
        if (!empty($parentItem)) {
            $offerDataItem = $parentItem->setOfferId($this->getOfferId());
        } else {
            $offerDataItem = $item;
        }

        if (!$this->saveOfferData($offerDataItem)) {
            return false;
        }

        $this->saveAdditionalInfoForMainProduct($item);
        return true;
    }
    
    public function isExistingNumberInOfferData($currentNumber = null)
    {
        if($currentNumber){
            $offerData = $this->getQuote()->getOfferData();
            if(isset($offerData)){
                $offerData = unserialize($offerData);
                foreach ($offerData as $offer){
                    if((isset($offer['number'])) && isset($currentNumber) && ($offer['number'] == $currentNumber)){
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function offerDataError($code = null)
    {
        $errorNameArray = array(
            'NUMBER_EXIST'        => 'This phone number already exists for another phone to Cart',
            'NO_ACTIVATION_TYPE'  => 'Not selected activation type',
            'NO_NEW_NUMBER'       => 'Not selected phone number',
            'NO_EXIST_NUMBER'     => 'Not selected phone number',
            'NO_SIM_TYPE'         => 'Not selected sim type',
            'default'             => 'Cannot add the item to shopping cart'
        );
        if (isset($errorNameArray[$code])) {
            $errorName = $errorNameArray[$code];
        } else {
            $errorName = $errorNameArray['default'];
        }
        $errorNameTrans = Mage::helper('tele4G_checkout')->__($errorName);
        Mage::log("offerDataError: \n{$errorNameTrans}", 1, 'offerDataError.log');
        $this->_getCheckout()->addError($errorNameTrans);
    }
    
    /**
     * setProductDataToQuoteItem
     * 
     * @param type $quoteItem
     * @param type $product
     */
    public function setProductDataToQuoteItem($quoteItem, $product)
    {
        $quoteItem->setExpectedDeliveryTime(Mage::app()->getRequest()->getParam('expectedDeliveryTime'));
        
        $_additionalData = $quoteItem->getAdditionalData();
        $additionalData = unserialize($_additionalData);
        
        $this->_setProductDataToQuoteItem($quoteItem, $product, $additionalData);
    }
    
    /**
     * getProductOfferFromQuote
     * 
     * @param type $attributeSetToFilter
     * @return $item or null
     */
    public function getProductOfferFromQuote($attributeSetToFilter = array())
    {
        if (empty($attributeSetToFilter)) {
            $attributeSetToFilter = array(
                Tele4G_Common_Helper_Data::ATTR_SET_DEVICE, 
                Tele4G_Common_Helper_Data::ATTR_SET_DONGLE,
                Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION,
            );
        }
        $param = $this->_getCheckout()->getOfferParamsAfterCart();
        if (isset($param['product'])) {
            $offerId = $this->_getCheckout()->getOfferId();
            $items = $this->getQuote()->getItemsCollection();
            foreach ($items as $item) {
                if ($item->getOfferId() == $offerId && $item->getProductId() == $param['product']) {
                    $attributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
                    if (in_array($attributeSetName, $attributeSetToFilter)) {
                        return $item;
                    }
                }
            }
        }
        return null;
    }

    protected function _setArticalId($quoteItem, $_product)
    {
        if (Mage::helper('tele2_catalog')->isSubscription($_product)) {
            if ($articleId = Mage::helper('tele4G_catalog/simonly')->getArticleId($quoteItem->getSku())) {
                /** SIM-only > Multiple SIM-types */
                $aId = $articleId;
            } else {
                $subscriptionData = Mage::helper('tele4G_subscription')->getSubscriptionData($quoteItem);
                $subscription = $subscriptionData['subscription'];
                $bindingPeriod = $subscriptionData['binding'];
                $binding = $subscription->getBindingByPeriod($bindingPeriod);
                $bindingArticleid = $binding->getArticleId();
                $aId = (!empty($bindingArticleid)) ? $bindingArticleid : $subscription->getArticleid();
            }
        } else {
            $aId = $_product->getArticleid();
        }
        if ($aId) {
            $quoteItem->setArticleId($aId);
        } else {
            throw new Exception('ArticleId is missing');
        }

    }

    public function getOffers()
    {
        $offers = array();
        $filterForOffers = $this->getOffersAttrSetName();
        $items = $this->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $atributeSetName = $this->_getAttributeSetNameById($item->getProduct()->getAttributeSetId());
            if (in_array($atributeSetName, $filterForOffers)) {
                $offers[] = $item;
            }
        }
        return $offers;
    }

    public function getOfferDataByItem($item = null)
    {
        $itemOfferData = array();
        if ($item) {
            $offerData = unserialize($this->getQuote()->getOfferData());
            $itemOfferData = $offerData[$item->getOfferId()];
        }
        return $itemOfferData;
    }
}
