<?php
class Tele2_FreeGift_Block_Adminhtml_FreeGift_Edit_Tab_ActionsTest extends PHPUnit_Framework_TestCase {
    public $actionBlock;

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();
        $this->actionBlock = $layout->createBlock('tele2_freeGift/adminhtml_freeGift_edit_tab_actions');
    }

    public function testgetDevices()
    {
        $result =  $this->actionBlock->getDevices();
        $this->assertInternalType('array', $result, 'Method does not return an array');
        $this->assertGreaterThanOrEqual(0, count($result), 'Result count is not in the range');
    }

    public function testgetAddons()
    {
        $result =  $this->actionBlock->getAddons();
        $this->assertInternalType('array', $result, 'Method does not return an array');
        $this->assertGreaterThanOrEqual(0, count($result), 'Result count is not in the range');
    }

    public function testgetAccessories()
    {
        $result =  $this->actionBlock->getAccessories();
        $this->assertInternalType('array', $result, 'Method does not return an array');
        $this->assertGreaterThanOrEqual(0, count($result), 'Result count is not in the range');
    }
}