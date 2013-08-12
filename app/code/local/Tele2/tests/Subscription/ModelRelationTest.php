<?php
class Tele2_Subscription_Model_RelationTest extends PHPUnit_Framework_TestCase {
    public $model;

    public function setUp()
    {
        $this->model = Mage::getModel('tele2_subscription/relation');
    }

    public function testgetProductsBySubscriptionGroup()
    {
        $type1Ids = array(1,2,3);
        $type2Ids = array(1,2,3,4);
        $categories = Mage::getModel('catalog/category')->getCollection();

        foreach ($categories as $category) {
            foreach ($type1Ids as $type1Id) {
                foreach ($type2Ids as $type2Id) {
                    $result = $this->model->getProductsBySubscriptionGroup($type1Id, $type2Id, $category->getId());
                    $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $result,
                        'Method returns wrong object for params '
                            . $type1Id . ', ' . $type2Id . ', ' . $category->getId());
                    $this->assertContainsOnlyInstancesOf('Mage_Catalog_Model_Product', $result,
                        'Collection does not contain product objects for params '
                            . $type1Id . ', ' . $type2Id . ', ' . $category->getId());
                    $this->assertNotEmpty($result);
                }
            }

        }
    }
}
