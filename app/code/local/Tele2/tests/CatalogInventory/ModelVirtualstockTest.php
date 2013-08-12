<?php
class Tele2_Subscription_Model_VirtualstockTest extends PHPUnit_Framework_TestCase {

    public function testGetVirtualStock()
    {
        $virtualStockModel = new Tele2_CatalogInventory_Model_Virtualstock(
            array(),
            $this->_getMockedConfigForVirtualStock()
        );

        $product = new Mage_Catalog_Model_Product();
        $product->setId(12);

        $this->assertEquals(
            $virtualStockModel->getVirtualStock($product),
            array( 2 => $this->_getVirtualStockData2(), 1 => $this->_getVirtualStockData1()));
    }

    public function testGetExpectedDeliveryTime()
    {
        $virtualStockModel = new Tele2_CatalogInventory_Model_Virtualstock(
            array(),
            $this->_getMockedConfigForVirtualStock()
        );

        $product = new Mage_Catalog_Model_Product();
        $product->setId(12);
        $this->assertEquals($virtualStockModel->getExpectedDeliveryTime($product), 7);
    }

    public function testGetExpectedDeliveryLevel()
    {
        $virtualStockModel = new Tele2_CatalogInventory_Model_Virtualstock(
            array(),
            $this->_getMockedConfigForVirtualStock()
        );

        $product = new Mage_Catalog_Model_Product();
        $product->setId(12);

        $this->assertEquals($virtualStockModel->getExpectedDeliveryLevel($product), $this->_getVirtualStockData1());
    }

    public function testDecreaseLeftInLevel()
    {
        $virtualStockModel = $this->getMock(
            'Tele2_CatalogInventory_Model_Virtualstock',
            array('save'),
            array(array('left' => 100))
        );

        $virtualStockModel->expects($this->once())
            ->method('save');

        $this->assertTrue($virtualStockModel->decreaseLeftInLevel(5));
        $this->assertEquals($virtualStockModel->getLeft(), 95);
    }

    public function testGetProductId()
    {
        $virtualStockModel = new Tele2_CatalogInventory_Model_Virtualstock();
        $product = new Mage_Catalog_Model_Product();
        $product->setId(12);
        $this->assertEquals($virtualStockModel->getProductId($product), 12);
        $this->assertEquals($virtualStockModel->getProductId(15), 15);
    }


    public function testDaysToWeeks()
    {
        $virtualStockModel = new Tele2_CatalogInventory_Model_Virtualstock();
        $this->assertEquals( $virtualStockModel->daysToWeeks(5), 0);
        $this->assertEquals( $virtualStockModel->daysToWeeks(7), 1);
        $this->assertEquals( $virtualStockModel->daysToWeeks(15), 2);
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForVirtualStock()
    {
        $virtualStockModelCollection = $this->getMock(
            'Tele2_CatalogInventory_Model_Resource_Virtualstock_Collection',
            array('addFieldToFilter', 'setOrder', 'getIterator'),
            array(), '', false
        );

        $virtualStockModelCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('product_id', 12)
            ->will($this->returnValue($virtualStockModelCollection));

        $virtualStockModelCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array(
                $this->_getVirtualStockData1(),
                $this->_getVirtualStockData2()
            ))));

        $virtualStockModelCollection->expects($this->once())
            ->method('setOrder')
            ->with('level', 'asc')
            ->will($this->returnValue($virtualStockModelCollection));

        $virtualStockModel = $this->getMock(
            'Tele2_CatalogInventory_Model_Virtualstock',
            array('getCollection'),
            array(), '', false
        );

        $virtualStockModel->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($virtualStockModelCollection));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('tele2_cataloginventory/virtualstock', array())
            ->will($this->returnValue($virtualStockModel));

        return $config;
    }

    /**
     * Get virtual stock data1
     *
     * @return array
     */
    protected function _getVirtualStockData1()
    {
        return new Varien_Object(
            array('level' => 1, 'expected_date' => date('Y-m-d', time() + 60 * 60 * 24 * 7), 'left' => 100)
        );
    }

    /**
     * Get virtual Stoc kData2
     *
     * @return array
     */
    protected function _getVirtualStockData2()
    {
        return new Varien_Object(
            array('level' => 2, 'expected_date' => date('Y-m-d', time() + 60 * 60 * 24 * 14), 'left' => 50)
        );
    }
}
