<?php
class Tele2_FreeGift_Block_Adminhtml_FreeGift_Edit_Tab_ConditionsTest extends PHPUnit_Framework_TestCase {
    public $conditionsBlock;

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();
        $this->conditionsBlock = $layout->createBlock('tele2_freeGift/adminhtml_freeGift_edit_tab_conditions');
    }

    public function testgetFreeGift()
    {
        Mage::unregister('freeGift');
        $this->assertNull($this->conditionsBlock->getFreeGift());

        $testValue = 'testvalue';
        Mage::register('freeGift', $testValue);

        $this->assertEquals($testValue, $this->conditionsBlock->getFreeGift(), 'Method returns incorrect value');
    }

    public function testgetDevices()
    {
        $result =  $this->conditionsBlock->getDevices();
        $this->assertInternalType('array', $result, 'Method does not return an array');
        $this->assertGreaterThanOrEqual(0, count($result), 'Result count is not in the range');
    }
}