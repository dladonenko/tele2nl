<?php
class Tele4G_Checkout_Model_CartTest extends PHPUnit_Framework_TestCase
{
    public $checkoutModel;

    public function setUp()
    {
        $this->checkoutModel = Mage::getModel('tele4G_checkout/cart');
    }
    
    public function testgetRulesForProduct()
    {
       $this->assertTrue(true);
    }

    public function testgetCouponRulesForProduct()
    {
       $this->assertTrue(true);
    }

    public function testgetCouponRules()
    {
        $testCouponsArray = array(
            null,
            '',
            'NONEXISTENT COUPON',
        );

        foreach ($testCouponsArray as $testCoupon) {
            $result = $this->checkoutModel->getCouponRules($testCoupon);
            $resultCount = $result->getSize();

            $this->assertInstanceOf('Tele2_FreeGift_Model_Resource_FreeGift_Collection',
                $result,
                'Method returns wrong object for coupon ' . $testCoupon);
            $this->assertContainsOnlyInstancesOf('Tele2_FreeGift_Model_FreeGift',
                $result,
                'Collection does not contain gift rule objects for coupon ' . $testCoupon);

            $this->assertGreaterThanOrEqual(0, $resultCount, 'Gift count is not in the range');
        }
    }
}