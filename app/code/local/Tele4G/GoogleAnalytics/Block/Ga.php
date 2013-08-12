<?php

/**
 * Tele4G GoogleAnalitics Page Block
 *
 * @category   Tele4G
 * @package    Tele4G_GoogleAnalytics
 */
class Tele4G_GoogleAnalytics_Block_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCode($accountId)
    {
        $_domain = Mage::getStoreConfig(Tele4G_GoogleAnalytics_Helper_Data::XML_PATH_DOMAIN);
                
        $pageName   = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && preg_match('/^\/.*/i', $pageName)) {
            $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
        }
        return "
_gaq.push(['_setAccount', '{$this->jsQuoteEscape($accountId)}']);
_gaq.push(['_setDomainName', '{$_domain}']);
_gaq.push(['_trackPageview'{$optPageURL}]);
";
    }

    /**
     * Render information about specified orders and their items
     *
     * @link https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce
     * @return string
     */
    protected function _getOrdersTrackingCode()
    {
        $orderIds = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analytics')->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
        ;
        $result = array();
        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }
            $result[] = sprintf("_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);",
                $order->getIncrementId(),
                $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount(),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCity())),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getRegion())),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCountry()))
            );
            foreach ($order->getAllVisibleItems() as $item) {
                $sku = $item->getSku();
                $subscription = null;
                $subscriptionItem = null;
                if (stripos($sku, '-subscr-')) {
                    //get subscription
                    $sku = $item->getProduct()->getSku();
                    $subscription = Mage::helper('tele2_subscription')->getSubscriptionBySku($item->getSku());
                    if ($subscription && $subscription->getId()) {
                        $subscriptionItem = sprintf("_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
                            $order->getIncrementId(),
                            $this->jsQuoteEscape($subscription->getId()),
                            $this->jsQuoteEscape($subscription->getName()),
                            null, // there is no "category" defined for the order item
                            $subscription->getPrice(), 1
                        );
                    }
                }

                $result[] = sprintf("_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
                    $order->getIncrementId(),
                    $this->jsQuoteEscape($sku),
                    $this->jsQuoteEscape($item->getName()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(), $item->getQtyOrdered()
                );

                if ($subscriptionItem) {
                    $result[] = $subscriptionItem;
                }
            }
            $result[] = "_gaq.push(['_trackTrans']);";
        }
        return implode("\n", $result);
    }
}