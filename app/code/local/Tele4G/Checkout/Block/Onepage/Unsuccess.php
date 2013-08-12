<?php
/**
 * One page checkout success page
 *
 * @category   Tele4G
 * @package    Tele4G_Checkout
 * @author     Ciklum
 */
class Tele4G_Checkout_Block_Onepage_Unsuccess extends Mage_Checkout_Block_Cart_Abstract
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function lastOrderId()
    {
        $lastId =  Mage::getSingleton('checkout/session')->getLastOrderId();        
        if ($lastId) {
            $order = Mage::getModel('sales/order');
            $order->load($lastId);
            $orderId = $order->getIncrementId();
        }
        return $orderId;        
    }
}