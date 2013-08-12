<?php
class Tele2_FreeGift_Block_Checkout_Cart_CouponTest extends PHPUnit_Framework_TestCase {
    public $couponBlock;

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();
        $this->couponBlock = $layout->createBlock('tele2_freeGift/checkout_cart_coupon');
    }

    public function testgetFreeGiftCouponCode()
    {
        $this->couponBlock->getQuote()->unsetData();
        $this->assertNull($this->couponBlock->getFreeGiftCouponCode(), 'Wrong result for quote without coupon');

        $testCoupon = 'testcoupon';
        $this->couponBlock->getQuote()->setFreeGiftCouponCode($testCoupon);

        $this->assertEquals($testCoupon, $this->couponBlock->getFreeGiftCouponCode(),
            'Wrong result for quote with test coupon');
    }
}
