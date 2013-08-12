<?php
class Klarna_Error_Test extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Mage::app('default');
    }
    
    protected function _getOrderMock()
    {
        $ii = 0;
        $orderIds = range(1, 19999);
        shuffle($orderIds);
        $order = Mage::getModel('sales/order');
        while ($order->load($orderId=array_shift($orderIds))) {
            $items = $order->getItemsCollection();
            if ($items->count()) {
                break;
            }
            if (++$ii == 999) break;
        }
        return $order;
    }
    
    public function testGetPaymentKlarnaError()
    {
        $order = $this->_getOrderMock();
        $offerData = array();
        $offerData['ss4_order']['errorName'] = 'ERROR_CREATING_INVOICE';
        $order->setOfferData(serialize($offerData));
        $result = Mage::helper("tele4G_checkout")->getPaymentKlarnaError($order);
        $this->assertEquals($result, "klarnaError");
    }

}