<?php

class Tele2_Subscription_Model_Observer
{
    /**
     * Is load
     * @var bool
     */
    private $_isLoad = false;

    /**
     * Save product subscription data
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveProductSubscriptionData(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->isConfigurable()) {
            $subscription = Mage::getModel('tele2_subscription/mobile');
            try {
                $manage = Mage::app()->getRequest()->getParam('manage');
                if (!empty($manage)) {
                    $subscriptionBind = array();
                    $newValues = array();
                    foreach ($manage['options'] as $option) {
                        if (isset($option['subscription']) && !$option['is_delete']) {
                            $subscription->load($option['subscription']);
                            $subscriptionDiscount = (double)$subscription->getData('price');
                            $totalSubscriptionDiscount = ($subscriptionDiscount * (int)$option['bindperiod']);// + (int)$option['discount']);

                            $optionSku = 'subscr-' . $option['subscription'] . '-' . $option['bindperiod'];
                            $newValues[$optionSku] = array(
                                'sku'    => $optionSku,
                                'price'  => $totalSubscriptionDiscount,
                                'title'  => 'subscr-' . $option['subscription'] . '-bind-' . $option['bindperiod'],
                                'price_type' => 'fixed'
                            );
                        }
                    }
                    if ($product->hasOptions()) {
                        foreach ($product->getOptions() as $productOption) {
                            if ($productOption->getTitle() == 'subscriptions') {
                                $productOption->delete();
                            }
                        }
                    }
                    if (count($newValues)) {
                        $productOption = Mage::getModel('catalog/product_option')
                            ->setProductId($product->getId())
                            ->setType('drop_down')
                            ->setTitle('subscriptions')
                            ->setValues($newValues)
                            ->save();
                    }
                }
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
}
