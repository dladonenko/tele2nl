<?php

class Tele4G_Sales_Model_Order extends Mage_Sales_Model_Order
{
    /**
     * Send email with order data
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail($isSend = false)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            if (!$isSend) {
                return $this;
            }
        }

        if ($this->getPayment()->getMethod() == 'tele4G_invoice' || $this->getPayment()->getMethod() == 'free' || $this->getShippingMethod() == 'togo_togo') {
            $this->setPaymentName('');
        } else {
            //$this->setPaymentCode($this->getPayment()->getMethodInstance()->getCode());
            $this->setPaymentName($this->getPayment()->getMethodInstance()->getTitle());
        }

        if ($this->getShippingMethod() == 'togo_togo') {
            $this->setIsToGo(true);
            $this->setShippingDescription(Mage::getStoreConfig('carriers/togo/name'));
            
            $this->applyTogoStoreInfo();
        }

        $sExpectedDeliveryTime = Mage::helper('tele4G_checkout')->getExpectedDeliveryTimeFromOrder($this);
        $this->setExpectedDeliveryTime($sExpectedDeliveryTime);

        return parent::sendNewOrderEmail();
    }
    
    public function applyTogoStoreInfo()
    {
        try {
            $togoHelper = Mage::helper("tele4G_togo");
            $offerData = unserialize($this->getOfferData());
            $resellerTogoInfo = $offerData['reseller_togo_info'];
            $this->setToGoCity($resellerTogoInfo['city']);
            $chain = Mage::helper('tele4G_checkout')->__($resellerTogoInfo['chain']);
            $this->setToGoType($chain);
            $this->setToGoReseller($resellerTogoInfo['address']);
            $openClose = $togoHelper->formatHours($resellerTogoInfo['openingTime']) . " - " . $togoHelper->formatHours($resellerTogoInfo['closingTime']);
            $this->setOpenClose($openClose);
            $url = "Öppet mellan " . $openClose . " (" . $chain . " " . $resellerTogoInfo['address'] . ") @" . round($resellerTogoInfo['latitude'], 7) . "," . round($resellerTogoInfo['longitude'], 7);
            $googleMapsUrl = "https://maps.google.com/maps?q=" . rawurlencode($url) . "&iwloc=&t=m&z=15";
            $this->setGoogleMapUrl($googleMapsUrl);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
    
    public function getSimpleFromItem($_item)
    {
        if ($_item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $childrenItems = $_item->getChildrenItems();
            if (count($childrenItems)) {
                return array_shift($childrenItems);
            }
        }
        return $_item;
    }
}
