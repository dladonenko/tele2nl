<?php
class Tele2_Subscription_Model_MobileTest extends PHPUnit_Framework_TestCase {

    public function testGenerateOption()
    {
        $mobileModel = new Tele2_Subscription_Model_Mobile(array(), $this->_getMockedConfig());
        $mobileModel->generateOption($this->_getMockedProduct());
    }

    public function testModifyOption()
    {
        $mobileModel = new Tele2_Subscription_Model_Mobile(array(), $this->_getMockedConfigForModifyOption());
        $mobileModel->modifyOption($this->_getMockedOptionForModifyOption(), $this->_getMockedProduct(), 1);
    }

    public function testGetAllSubscriptions()
    {
        $mobileModel = new Tele2_Subscription_Model_Mobile(array(), $this->_getMockedConfigForGetAllSubscriptions());
        $this->assertInstanceOf('Tele2_Subscription_Model_Resource_Mobile_Collection', $mobileModel->getAllSubscriptions());
    }

    public function testGetSubscriptionByProductId()
    {
        $productId = 1;
        $mobileModel = $this->getMock(
            'Tele2_Subscription_Model_Mobile',
            array('loadByFakeProduct'),
            array(), '', false
        );
        $mobileModel->expects($this->once())
            ->method('loadByFakeProduct')
            ->with($productId);

        $mobileModel->getSubscriptionByProductId($productId);
    }

    public function testGetPrice()
    {
        $mobileModel = $this->getMock(
            'Tele2_Subscription_Model_Mobile',
            array('getParamBindPeriod', 'getBindings'),
            array(), '', false
        );

        $mobileModel->expects($this->once())
            ->method('getParamBindPeriod')
            ->will($this->returnValue(24));

        $bindings = array(
            new Varien_Object(array('time' => 12, 'monthly_price_with_vat' => 100)),
            new Varien_Object(array('time' => 24, 'monthly_price_with_vat' => 200)),
        );

        $mobileModel->expects($this->once())
            ->method('getBindings')
            ->will($this->returnValue($bindings));

        $this->assertEquals($mobileModel->getPrice(), 200);

    }

    /**
     * Get a option mocked model
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function _getMockedOptionForModifyOption()
    {
        $option = $this->getMock(
            'Mage_Catalog_Model_Product_Option',
            array('getValues', 'delete'),
            array(), '', false
        );

        $values = array(
            new Varien_Object(
                array('sku' => 'subscr-1-bind-12', 'price' => 100)
            )
        );

        $option->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue($values));

        return $option;
    }


    /**
     * Get a option mocked model
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function _getMockedOption()
    {
        $option = $this->getMock(
            'Mage_Catalog_Model_Product_Option',
            array('setProductId', 'setType', 'setTitle', 'setValues', 'save', 'getValuesCollection'),
            array(), '', false
        );

        $option->expects($this->once())
            ->method('setProductId')
            ->with(1)
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setType')
            ->with('drop_down')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setTitle')
            ->with('subscriptions')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setValues')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('save');

        $option->expects($this->once())
            ->method('getValuesCollection')
            ->will($this->returnValue(array()));

        return $option;
    }

    /**
     * Get a config mocked model
     *
     * @return Tele2_Subscription_Model_Resource_Binding_Collection
     */
    protected function _getMockedConfigForModifyOption()
    {
        $bindingCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_Binding_Collection',
            array('count', 'getIterator', 'filterBySubscription'),
            array(),
            '',
            false
        );

        $bindingCollection->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $bindingCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array(
                new Varien_Object(
                    array('time' => time())
                )
            ))));

        $config = $this->getMock('Mage_Core_Model_Config', array('getResourceModelInstance', 'getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/binding_collection', array())
            ->will($this->returnValue($bindingCollection));


        $option = $this->getMock(
            'Mage_Catalog_Model_Product_Option',
            array('setProductId', 'setType', 'setTitle', 'setValues', 'save', 'getValuesCollection'),
            array(), '', false
        );

        $option->expects($this->once())
            ->method('setProductId')
            ->with(1)
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setType')
            ->with('drop_down')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setTitle')
            ->with('subscriptions')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('setValues')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('save')
            ->will($this->returnValue($option));

        $option->expects($this->once())
            ->method('getValuesCollection')
            ->will($this->returnValue(array()));


        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('catalog/product_option', array())
            ->will($this->returnValue($option));

        return $config;
    }


    /**
     * Get a config mocked model
     *
     * @return Tele2_Subscription_Model_Resource_Binding_Collection
     */
    protected function _getMockedConfig()
    {
        $bindingCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_Binding_Collection',
            array('filterBySubscription', 'count', 'getValuesCollection'),
            array(), '', false
        );

        $bindingCollection->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance', 'getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('catalog/product_option')
            ->will($this->returnValue($this->_getMockedOption()));

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/binding_collection', array())
            ->will($this->returnValue($bindingCollection));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Tele2_Subscription_Model_Resource_Binding_Collection
     */
    protected function _getMockedConfigForGetAllSubscriptions()
    {
        $bindingCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_Mobile_Collection',
            array(),
            array(), '', false
        );

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance', 'getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/mobile_collection', array())
            ->will($this->returnValue($bindingCollection));

        return $config;
    }

    /**
     * Get mocked product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function _getMockedProduct()
    {
        $product = $this->getMock('Mage_Catalog_Model_Product', array('save', 'getId'), array(), '', false);

        $product->expects($this->any())->method('save');

        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        return $product;
    }
}
