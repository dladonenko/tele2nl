<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Model_AddonRelation extends Mage_Core_Model_Abstract
{
    /**
     * List of add ons ids
     *
     * @var null|array
     */
    private $_addonIds = null;
    private $_productCollection = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('tele2_subscription/addonRelation');
    }

    /**
     * Get subscription add ons
     *
     * @param int $subscriptionId
     * @return array|null
     */
    public function getSubscriptionAddons($subscriptionId) 
    {
        if (is_null($this->_addonIds)) {
            $subscription = Mage::getModel('tele2_subscription/mobile')->load($subscriptionId);
            $collection = $subscription->getAddons($subscription);

            $this->_addonIds = array();
            foreach ($collection as $relation)
            {
                if (!in_array($relation->getAddonId(), $this->_addonIds))
                    $this->_addonIds[] = $relation->getAddonId();
            }
        }
        return $this->_addonIds;
    }

    /**
     * Save add ons
     * @param int $subscription
     * @param Zend_Controller_Request_Http $request
     * @return $this
     */
    public function saveAddons($subscription, $request)
    {
        $selectedAddons = $request->getPost('selected_addon');
//        if (!count($selectedAddons)) {
//            return;
//        }
        if (
            !($subscription instanceof Tele2_Subscription_Model_Subscription) &&
            is_int($subscription) && $subscription
        ) {
            $subscription = Mage::getModel('tele2_subscription/mobile')->load($subscription);
        }
        $newAddons = array();
        $oldAddons = array();

        if ($selectedAddons && count($selectedAddons)) {
            foreach ($selectedAddons as $_addon) {
                if ((int)$_addon) {
                    $newAddons[] = $_addon;
                }
            }
        }

        $links = $request->getPost('links');
        if (isset($links['related_addons']) && count($links['related_addons'])) {
            $relatedAddons = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related_addons']);
            $relatedAddons = array_keys($relatedAddons);
            $newAddons = array_merge($newAddons, $relatedAddons);
            foreach ($subscription->getAddons($subscription) as $_addon) {
                if (in_array($_addon->getAddonId(), $newAddons)) {
                    $oldAddons[] = $_addon->getAddonId();
                } else {
                    $_addon->delete();
                }
            }
            foreach ($newAddons as $_addon) {
                if (!in_array($_addon, $oldAddons)) {
                    $add = $this->addData(array(
                        'addon_id'        => $_addon,
                        'subscription_id' => $subscription->getSubscriptionId(),
                        'stype_id'        => $subscription->getType1(),
                    ))->save();
                    $add->unsetData();
                }
            }
        }
        return $this;
    }
}
