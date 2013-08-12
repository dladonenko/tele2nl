<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_FreeGift
 */


class Tele2_FreeGift_Block_Checkout_Cart_Coupon extends Mage_Checkout_Block_Cart_Abstract
{
    public function getFreeGiftCouponCode()
    {
        return $this->getQuote()->getFreeGiftCouponCode();
    }


}
