<?php
class Tele2_Subscription_Model_AddonRelationTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = Mage::getModel('tele2_subscription/addonRelation');
    }

    public function testgetSubscriptionAddons()
    {
        $subscriptionIdsArray = range(1, 100);
        foreach ($subscriptionIdsArray as $key=>$val) {
            $subscriptionAddons = $this->model->getSubscriptionAddons($val);
            $this->assertInternalType('array', $subscriptionAddons,
                'Method does not return array for testarray key ' . $key);
            $this->assertGreaterThanOrEqual(0, count($subscriptionAddons), 'Addons count is not in the range');
        }
    }
}
