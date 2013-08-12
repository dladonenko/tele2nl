<?php
class Tele2_FreeGift_Helper_DataTest extends PHPUnit_Framework_TestCase {
    public $helper;

    public function setUp()
    {
        $this->helper = Mage::helper('tele2_freeGift');
    }

    public function testgetBindingPeriods()
    {
        $testSubscriptionIdsArray = range(-1,100);
        $testSubscriptionIdsArray['null'] = null;
        $testSubscriptionIdsArray['string'] = 'string';

        foreach ($testSubscriptionIdsArray as $key=>$val) {
            $result = $this->helper->getBindingPeriods($val);
            $this->assertInternalType('array', $result,
                'Method does not return array for testarray key ' . $key);
            $this->assertGreaterThanOrEqual(0, count($result), 'No bindings for testarray key ' . $key);
        }
    }

    public function testgetSubscriptionAsOptions()
    {
        $testWithEmptyOptionArray = array(true, false, null);
        foreach ($testWithEmptyOptionArray as $val) {
            $result = $this->helper->getSubscriptionAsOptions($val);
            $this->assertInternalType('array', $result, 'Method does not return array for testarray key ' . $val);
            $this->assertGreaterThanOrEqual(0, count($result), 'No subscriptions for testarray key ' . $val);
        }
    }

    public function testgetProductsAsOptions()
    {
        $testProductTypeArray = array(
            Tele2_Install_Helper_Data::ATTR_SET_ADDON,
            Tele2_Install_Helper_Data::ATTR_SET_ACCESSORY,
            Tele2_Install_Helper_Data::ATTR_SET_DEVICE
        );
        $testWithEmptyOptionArray = array(true, false, null);

        foreach ($testProductTypeArray as $type) {
            foreach ($testWithEmptyOptionArray as $val) {
                $result = $this->helper->getProductsAsOptions($type, $val);
                $this->assertInternalType('array', $result, 'Method does not return array for params ' . $type . ' and ' . $val);
                $this->assertGreaterThanOrEqual(0, count($result), 'No products for params ' . $type . ' and ' . $val);
            }
        }
    }
}
