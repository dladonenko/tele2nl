<?php
/**
 * Tele4G checkout cart link
 *
 * @category   Tele4G
 * @package    Tele4G_Checkout
  */

class Tele4G_Checkout_Block_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/tele4G', array('_secure'=>true));
    }
}
