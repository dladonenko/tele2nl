<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Model_ConfigRelation extends Mage_Core_Model_Abstract
{
    /**
     * Config Ids
     *
     * @var null|array
     */
    private $_configIds = null;

    /**
     * Product collection
     *
     * @var null|Mage_Catalog_Model_Resource_Product_Collection
     */
    private $_productCollection = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('tele2_subscription/configRelation');
    }

    /**
     * Get subscription configs
     *
     * @param $subscriptionId
     * @return array|null
     */
    public function getSubscriptionConfigs($subscriptionId) 
    {
        if (is_null($this->_configIds)) {
            $subscription = Mage::getModel('tele2_subscription/mobile')->load($subscriptionId);
            $collection = $subscription->getConfigs($subscription);

            $this->_configIds = array();
            foreach ($collection as $relation)
            {
                if (!in_array($relation->getconfigId(), $this->_configIds))
                    $this->_configIds[] = $relation->getConfigId();
            }
        }
        return $this->_configIds;
    }

    /**
     * Save Configs
     *
     * @param Tele2_Subscription_Model_Subscription|int $subscription
     * @param Zend_Controller_Request_Http $request
     * @return $this
     */
    public function saveConfigs($subscription, $request)
    {
        $selectedConfigs = $request->getPost('selected_config');
        if (!count($selectedConfigs)) {
            return;
        }
        if (
            !($subscription instanceof Tele2_Subscription_Model_Subscription) &&
            is_int($subscription) && $subscription
        ) {
            $subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription);
        }
        $newConfig = array();
        $oldConfigs = array();

        foreach ($selectedConfigs as $_config) {
            if ((int)$_config) {
                $newConfig[] = $_config;
            }
        }
        $links = $request->getPost('links');
        $relatedConfigs = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related_configs']);
        $relatedConfigs = array_keys($relatedConfigs);
        $newConfig = array_merge($newConfig, $relatedConfigs);
        foreach ($subscription->getConfigs($subscription) as $_config) {
            if (in_array($_config->getConfigId(), $newConfig)) {
                $oldConfigs[] = $_config->getConfigId();
            } else {
                $_config->delete();
            }
        }
        foreach ($newConfig as $_config) {
            if (!in_array($_config, $oldConfigs)) {
                $add = $this->addData(array(
                    'config_id'        => $_config,
                    'subscription_entity_id' => $subscription->getSubscriptionId(),
                ))->save();
                $add->unsetData();
            }
        }
        return $this;
    }
}
