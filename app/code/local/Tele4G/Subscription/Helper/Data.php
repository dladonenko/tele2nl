<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele4G_Subscription_Helper_Data extends Tele2_Subscription_Helper_Data
{
    private $_subscription = null;

    /**
     * @param $_item
     * @return array
     */
    public function getSubscriptionData($_item)
    {
        $subscriptionData = array();
        $subscriptionId = null;

        if (Mage::helper('tele2_catalog')->isSubscription($_item->getProduct()->getAttributeSetId())) {
            $subscriptionData['subscription'] = Mage::getModel('tele2_subscription/mobile')->load($_item->getProduct()->getId(), 'fake_product_id');
            $subscriptionData['binding'] = 0;
        } else {
            $itemOptions = $_item->getProductOptions();
            if (
                isset($itemOptions['options'])
            ) {
                foreach ($itemOptions['options'] as $option) {
                    if (
                        isset($option['label']) &&
                        $option['label'] == 'subscriptions'
                    ) {
                        if (preg_match("%subscr-(\d+)-bind-(\d+)%", $option['value'], $m)) {
                            $subscriptionId = $m[1];
                            $subscriptionData['binding'] = $m[2];
                        }
                    }
                }
            }
            if ($subscriptionId) {
                if (!$this->_subscription) {
                    $this->_subscription = Mage::getModel('tele2_subscription/mobile');
                }
                $subscriptionData['subscription']
                    = $this->_subscription->load($subscriptionId);
            }
        }
        return $subscriptionData;
    }
}
