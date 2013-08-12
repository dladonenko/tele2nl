<?php

class Tele4G_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    protected $_bindings = array();
    
    public function getPrePostSubscriptionPrice($_product)
    {
        $options = $_product->getOptions();
        $helperCommon = Mage::helper("tele4G_common/data");

        $subscription_pre = array();
        $subscription_post = array();

        $aCollection = array();

        if ($options) {
            foreach ($options as $option) {
                if ($option->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS) {
                    foreach ($option->getValues() as $value)
                    {
                        $sku = $value->getSku();
                        if (preg_match("%subscr-(\d+)-(\d+)%", $sku, $m)) {
                            $subscription_id = $m[1];
                            $bind_period = $m[2];
                            
                            $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                            if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE) {
                                $subscription_pre[$_subscription->getSubscriptionId()]['price'] = (($_product->getPrice() + $value->getPrice()) < 0 ? 0 : ($_product->getPrice() + $value->getPrice()));
                                $subscription_pre[$_subscription->getSubscriptionId()]['monthly_price'] = $_subscription->getPrice();
                            } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {
                                $subscription_post[$_subscription->getSubscriptionId()]['price'] = (($_product->getPrice() + $value->getPrice()) < 0 ? 0 : ($_product->getPrice() + $value->getPrice()));
                                $subscription_post[$_subscription->getSubscriptionId()]['monthly_price'] = $_subscription->getPrice();
                            }
                        }
                    }
                    if (!empty($subscription_pre)) {
                        foreach ($subscription_pre as $subscription_pre_key => $subscription_pre_value) {
                            $prices_pre[$subscription_pre_key] = $subscription_pre_value['price'];
                        }
                        array_multisort($prices_pre, SORT_ASC, $subscription_pre);
                        $aCollection['pre'] = current($subscription_pre);
                    }
                    if (!empty($subscription_post)) {
                        foreach ($subscription_post as $subscription_post_key => $subscription_post_value) {
                            $prices_post[$subscription_post_key] = $subscription_post_value['price'];
                        }
                        array_multisort($prices_post, SORT_ASC, $subscription_post);
                        $aCollection['post'] = current($subscription_post);
                    }
                    return $aCollection;
                }
            }
        }
        return false;
    }
    
    public function getPrePostSubscription($_product)
    {
        $options = $_product->getOptions();
        $helperCommon = Mage::helper("tele4G_common/data");

        $subscription_pre = array();
        $subscription_post = array();

        $aCollection = array();

        if ($options) {
            foreach ($options as $option) {
                if ($option->getDefaultTitle() == $helperCommon::CUSTOM_OPTION_SUBSCRIPTIONS) {
                    foreach ($option->getValues() as $value)
                    {
                        $sku = $value->getSku();
                        if (preg_match("%subscr-(\d+)-(\d+)%", $sku, $m)) {
                            $subscription_id = $m[1];
                            $bind_period = $m[2];

                            $_subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription_id);
                            $_subscription->setParamBindPeriod($bind_period);
                            
                            if ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE ||
                                $_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE) {
                                
                                $binding = $_subscription->getBindingByPeriod($bind_period);

                                if ($binding) {
                                    $subscription_pre[$_subscription->getSubscriptionId()]['subscription_id'] = $_subscription->getSubscriptionId();
                                    $subscription_pre[$_subscription->getSubscriptionId()]['name'] = $_subscription->getName();

                                    $price = (($_product->getPrice() + $value->getPrice()) < 0 ? 0 : ($_product->getPrice() + $value->getPrice()));

                                    $subscription_pre[$_subscription->getSubscriptionId()]['price'] = $price;
                                    $subscription_pre[$_subscription->getSubscriptionId()]['option_id'] = $value->getOptionId();
                                    $subscription_pre[$_subscription->getSubscriptionId()]['bind_period'] = $bind_period;
                                    $subscription_pre[$_subscription->getSubscriptionId()]['value_id'] = $value->getId();

                                    $subscription_pre[$_subscription->getSubscriptionId()]['value_ids'][$bind_period] = $value->getId();
                                    $subscription_pre[$_subscription->getSubscriptionId()]['bind_price'][$bind_period] = $value->getPrice();

                                    $subscription_pre[$_subscription->getSubscriptionId()]['least_total_cost'][$bind_period] = $price;
                                    $subscription_pre[$_subscription->getSubscriptionId()]['monthly_price'] = $_subscription->getPrice();
                                }
                            } elseif ($_subscription->getType1() == Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST) {

                                $subscription_post[$_subscription->getSubscriptionId()]['selected'] = ""; //(isset($subscription_post[$_subscription->getSubscriptionId()]['selected'])? $subscription_post[$_subscription->getSubscriptionId()]['selected'] : '');
                                $subscription_post[$_subscription->getSubscriptionId()]['subscription_id'] = $_subscription->getSubscriptionId();
                                $subscription_post[$_subscription->getSubscriptionId()]['name'] = $_subscription->getName();

                                $subscription_post[$_subscription->getSubscriptionId()]['value_ids'][$bind_period] = $value->getId();
                                $subscription_post[$_subscription->getSubscriptionId()]['bind_price'][$bind_period] = $value->getPrice();

                                $price = (($_product->getPrice() + $value->getPrice()) < 0 ? 0 : ($_product->getPrice() + $value->getPrice()));
                                $subscription_post[$_subscription->getSubscriptionId()]['least_total_cost'][$bind_period] = ($price + $_subscription->getPrice() * $bind_period);

                                $subscription_post[$_subscription->getSubscriptionId()]['bind_period'][] = $bind_period;
                                $subscription_post[$_subscription->getSubscriptionId()]['monthly_price'] = $_subscription->getPrice();
                                $subscription_post[$_subscription->getSubscriptionId()]['bind_monthly_prices'] = $_subscription->getBindPrices();
                            }
                        }
                    }
                    if (!empty($subscription_pre)) {
                        $aCollection['pre'] = $subscription_pre;
                        $aCollection['option_id'] = $option->getId();
                    }
                    if (!empty($subscription_post)) {
                        $aCollection['post'] = $subscription_post;
                        $aCollection['option_id'] = $option->getId();
                    }
                    return $aCollection;
                }
            }
        }
        return false;
    }
}
