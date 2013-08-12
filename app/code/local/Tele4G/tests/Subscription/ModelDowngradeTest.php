<?php
class Tele4G_Subscription_Model_DowngradeTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = Mage::getModel('tele4G_subscription/downgrade');
    }

    public function testgetSubscriptionCodeA()
    {
        $result = $this->model->getSubscriptionCodeA();
        $this->assertThat(
            $result,
            $this->logicalOr(
                $this->isInstanceOf('Mage_Catalog_Model_Resource_Product_Collection'),
                $this->isFalse()
            ),
            'Method returns wrong collection'
        );
    }

    public function testgetSubscriptionCodeGN0()
    {
        $result = $this->model->getSubscriptionCodeGN0();
        $this->assertThat(
            $result,
            $this->logicalOr(
                $this->isInstanceOf('Mage_Catalog_Model_Resource_Product_Collection'),
                $this->isFalse()
            ),
            'Method returns wrong collection'
        );
    }

    public function testgetSubscriptionCodeGN1Post()
    {
        $result = $this->model->getSubscriptionCodeGN1Post();
        $this->assertThat(
            $result,
            $this->logicalOr(
                $this->isInstanceOf('Mage_Catalog_Model_Resource_Product_Collection'),
                $this->isFalse()
            ),
            'Method returns wrong collection'
        );
    }



}
