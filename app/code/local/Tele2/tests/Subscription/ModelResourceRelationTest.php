<?php
class Tele2_Subscription_Model_Resource_RelationTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = Mage::getResourceModel('tele2_subscription/relation');
    }

    public function testgetSubscriptionProductIds()
    {
        $subscriptions = Mage::getModel('tele2_subscription/subscription')->getCollection();
        foreach ($subscriptions as $subscription) {
            $result = $this->model->getSubscriptionProductIds($subscription->getId());
            $this->assertInternalType('array', $result, 'Bad result');
            $this->assertGreaterThanOrEqual(0, count($result), 'Bad result count');
        }
    }
}
