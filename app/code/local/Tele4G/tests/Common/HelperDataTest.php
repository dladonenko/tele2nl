<?php
class Tele4G_Common_Helper_DataTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Mage::app('default');
        $this->_helper = Mage::helper('tele4G_common/data');
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
    protected function getAttributeSetNames()
    {
        $helperCommon = $this->_helper;
        return array(
            $helperCommon::ATTR_SET_DEVICE,
            $helperCommon::ATTR_SET_DONGLE,
            $helperCommon::ATTR_SET_ACCESSORY,
            $helperCommon::ATTR_GROUP_ADDON,
            $helperCommon::ATTR_SET_SUBSCRIPTION,
            "undefined" => "ovrigt"
        );
    }
    
    protected function getRealSnn()
    {
        $arraySsn = array('198609050417','197002240468','197404090230','198403103610');
        shuffle($arraySsn);
        return array_shift($arraySsn);
    }

    protected function getFakeSnn()
    {
        $arraySsn = array('123456789123','000000000000','999999999999');
        shuffle($arraySsn);
        return array_shift($arraySsn);
    }

    public function testGetOrderItems()
    {
        $orderItems = $this->_helper->getOrderItems($this->_getOrderMock());
        $this->assertInternalType('string', $orderItems);
        $this->assertNotEmpty($orderItems);
    }
    
    public function testGetCity()
    {
        $city = $this->_helper->getCity($this->_getOrderMock());
        $this->assertInternalType('string', $city);
        $this->assertNotEmpty($city);
    }
    
    public function testGetPaymentMethod()
    {
        $paymentMethod = $this->_helper->getPaymentMethod($this->_getOrderMock());
        $this->assertInternalType('string', $paymentMethod);
        $this->assertNotEmpty($paymentMethod);
    }
    
    public function testGetOrderId()
    {
        $orderId = $this->_helper->getOrderId($this->_getOrderMock());
        $this->assertInternalType('string', $orderId);
        $this->assertNotEmpty($orderId);
    }
    
    public function testGetTrackingPointName()
    {
        foreach ($this->getAttributeSetNames() as $attributeSetName) {
            $trackingPointName = $this->_helper->getTrackingPointName($attributeSetName);
            $this->assertInternalType('string', $trackingPointName);
            $this->assertNotEmpty($trackingPointName);
        }
    }

    public function testCreateOrder()
    {
        $ageGroup = "B";
        $genderCode = "K";
        $city = "Stockholm";
        $paymentMethod = "free";
        $orderId = "TC9537186";
        $order1 = $this->_helper->createOrder($ageGroup, $genderCode, $city, $paymentMethod, $orderId);
        $this->assertInternalType('string', $order1);
        $this->assertNotEmpty($order1);
        
        $orderId = "";
        $order2 = $this->_helper->createOrder($ageGroup, $genderCode, $city, $paymentMethod, $orderId);
        $this->assertInternalType('string', $order2);
        $this->assertNotEmpty($order2);
    }
   
    public function testAddProduct()
    {
        $type = "PostPaid Comviq Voice";
        $deviceName = "Nexus 4";
        $bundleName = "nexus-4";
        $type_product = "mobiltelefoner";

        $this->_helper->addProduct($type, $deviceName, $bundleName, $type_product);
        $jsArrayProduct = $this->_helper->getJsArrayProduct();
        $this->assertInternalType('array', $jsArrayProduct);
        $this->assertNotEmpty($jsArrayProduct);
    }
}
