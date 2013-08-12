<?php
/**
 * One page checkout Resellers block
 *
 * @category   Tele4G
 * @package    Tele4G_Checkout
 * @author     Ciklum
 */
class Tele4G_Checkout_Block_Onepage_Shippingmethods_Resellertogo extends Mage_Checkout_Block_Onepage
{
    
    public function getResellers()
    {
        $_resellers = array();
        $productOffer = Mage::getModel("tele4G_checkout/offer")->getProductOfferFromQuote();
        if ($productOffer) {
            $catalogHelper = Mage::helper('tele2_catalog');
            $ss4IntegrationHelper = Mage::helper("tele4G_sS4Integration/data");
            $logisticId = ($productOffer->getPartnerId()) ? $productOffer->getPartnerId() : $productOffer->getArticleId() ;
            $ss4Result = $ss4IntegrationHelper->getResellersForArticleAndCity(array("city" => $this->getCity(), "article_id" => $logisticId));
            $resellers = null;
            if (isset($ss4Result->result->resellers->reseller)) {
                $resellers = $ss4Result->result->resellers->reseller;
                if (is_array($ss4Result->result->resellers->reseller)) {
                    foreach ($resellers as $reseller) {
                        if ((isset($reseller->stockLevelAmount) && $reseller->stockLevelAmount > 0) || $catalogHelper->isSubscription($productOffer->getProduct())) {
                            $_resellers[$reseller->distributingResellerNumber] = (array)$reseller;
                        }
                    }
                } elseif ($resellers instanceof stdClass) {
                    if ((isset($resellers->stockLevelAmount) && $resellers->stockLevelAmount > 0) || $catalogHelper->isSubscription($productOffer->getProduct())) {
                        $_resellers[$resellers->distributingResellerNumber] = (array)$resellers;
                    }
                }
            }
        }
        Mage::getSingleton("checkout/session")->setResellersToGoInfo($_resellers);
        
        return $_resellers;
    }
}