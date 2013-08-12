<?php
class Tele2_Subscription_Model_MobileTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = Mage::getModel('tele2_subscription/mobile');
    }

    public function testgetAllSubscriptions()
    {
        $subscriptions = $this->model->getAllSubscriptions();
        $this->assertInstanceOf('Tele2_Subscription_Model_Resource_Mobile_Collection',
            $subscriptions,
            'Method returns wrong object');
        $this->assertContainsOnlyInstancesOf('Tele2_Subscription_Model_Mobile',
            $subscriptions,
            'Collection does not contain product objects');
        $this->assertNotEmpty($subscriptions);
    }

    public function testgetRelatedProducts()
    {
        $subscription = $this->model;
        $testIds = range(1, 50);

        foreach ($testIds as $key=>$val) {
            $subscription->setId($val); //???
            $relatedProducts = $subscription->getRelatedProducts();
            $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection',
                $relatedProducts,
                'Method returns wrong object for testarray key ' . $key);
            $this->assertContainsOnlyInstancesOf('Mage_Catalog_Model_Product',
                $relatedProducts,
                'Collection does not contain product objects for test array key ' . $key);
            $this->assertNotEmpty($relatedProducts);
        }
    }

    public function testgetBindings()
    {
        $testIds = range(0, 100);
        foreach ($testIds as $key=>$val) {
            $subscription = $this->model->setId($val);
            $bindings = $subscription->getBindings();

            $this->assertThat(
                $bindings,
                $this->logicalOr(
                    $this->isFalse(),
                    $this->isType('array')
                ),
                'Method returns wrong bindings for test array key ' . $key
            );
        }
    }

    public function testgetBindingById()
    {
        $subscriptionIds = range(19, 47);
        foreach ($subscriptionIds as $key=>$val) {
            $subscription = $this->model->setId($val);

            $bindingIds = range(45, 100);
            foreach ($bindingIds as $bKey=>$bVal) {
                $binding = $subscription->getBindingById($bVal);

                $this->assertThat(
                    $binding,
                    $this->logicalOr(
                        $this->isFalse(),
                        $this->isInstanceOf('Tele2_Subscription_Model_Binding')
                    ),
                    'Method returns wrong binding for test array keys ' . $key . ', ' . $bKey
                );
            }
        }
    }

    public function testgetBindingByPeriod()
    {
        $subscriptionIds = range(0, 100);
        foreach ($subscriptionIds as $key=>$val) {
            $subscription = $this->model->setId($val);

            $bindingPeriods = array('0'=>0, 12, 18, 24, 36);
            foreach ($bindingPeriods as $bKey=>$bVal) {
                $binding = $subscription->getBindingByPeriod($bVal);

                $this->assertThat(
                    $binding,
                    $this->logicalOr(
                        $this->isFalse(),
                        $this->isInstanceOf('Tele2_Subscription_Model_Binding')
                    ),
                    'Method returns wrong binding for test array keys ' . $key . ', ' . $bKey
                );
            }
        }
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

    public function testgetSubscriptionByProductId()
    {
        $productIds = range(1, 2000);
        foreach ($productIds as $key=>$val) {
            $subscriptions = $this->model->getSubscriptionByProductId($val);
            $this->assertInstanceOf('Tele2_Subscription_Model_Mobile',
                $subscriptions,
                'Method returns wrong object for test array key ' . $key);
            $this->assertNotEmpty($subscriptions);
        }
    }

    public function testgetPrice()
    {
        $bindings = array(0, 12, 24, 36);
        foreach ($bindings as $binding) {
            $subscription = $this->model->setParamBindPeriod($binding);
            $this->assertGreaterThanOrEqual(0, $subscription->getPrice(), 'Bad price for binding ' . $binding);
        }
    }

    public function testgetBindPrices()
    {
        $prices = $this->model->getBindPrices();
        $this->assertInternalType('array', $prices, 'Bad result');
        $this->assertGreaterThanOrEqual(0, count($prices), 'Bad result count');
    }

    public function testgetAddons()
    {
        $subscriptions = $this->model->getCollection();
        foreach ($subscriptions as $subscription) {
            $this->model->setId($subscription->getId());
            $addonsArray = $this->model->getAddons();
            $this->assertInstanceOf('Tele2_Subscription_Model_Resource_AddonRelation_Collection', $addonsArray,
                'Method returns wrong object');
            $this->assertGreaterThanOrEqual(0, count($addonsArray),
                'Bad result count for subscription entity ' . $subscription->getId());
        }
    }

    public function testgetConfigs()
    {
        $subscriptions = $this->model->getCollection();
        foreach ($subscriptions as $subscription) {
            $this->model->setId($subscription->getId());
            $configsArray = $this->model->getConfigs();
            $this->assertInstanceOf('Tele2_Subscription_Model_Resource_ConfigRelation_Collection', $configsArray,
                'Method returns wrong object');
            $this->assertGreaterThanOrEqual(0, count($configsArray),
                'Bad result count for subscription entity ' . $subscription->getId());
        }
    }

    public function testgetSubscriptionGroup()
    {
        $subscriptions = $this->model->getCollection();
        foreach ($subscriptions as $subscription) {
            $this->model->setId($subscription->getId());
            if ($subscription->getData('subscription_group')) {
                $subscriptionGroup = $this->model->getSubscriptionGroup();
                $this->assertInstanceOf('Tele2_Subscription_Model_Group', $subscriptionGroup,
                    'Method returns wrong object');
            }
        }
    }
}
