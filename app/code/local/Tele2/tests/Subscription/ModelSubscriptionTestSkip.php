<?php
class Tele2_Subscription_Model_SubscriptionTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = $this->getMockForAbstractClass('Tele2_Subscription_Model_Subscription');
    }

    public function testgetSubscriptionProductsCollection()
    {
        $subscriptionProducts = $this->model->getSubscriptionProductsCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection',
            $subscriptionProducts,
            'Method returns wrong object');
        $this->assertContainsOnlyInstancesOf('Mage_Catalog_Model_Product',
            $subscriptionProducts,
            'Collection does not contain product objects');
        $this->assertNotEmpty($subscriptionProducts);
    }


}
