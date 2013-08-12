<?php
class Tele4G_Catalog_Helper_DataTest extends PHPUnit_Framework_TestCase {
    public $helper;
    public $dongleAttrSetId;
    public $deviceAttrSetId;
    public $subscriptionAttrSetId;
    public $defaultAttrSetId;

    public function setUp()
    {
        $this->helper = Mage::helper('tele2_catalog');

        $dongleAttributeSetName = Tele4G_Common_Helper_Data::ATTR_SET_DONGLE;
        $deviceAttributeSetName = Tele4G_Common_Helper_Data::ATTR_SET_DEVICE;
        $subscriptionAttributeSetName = Tele4G_Common_Helper_Data::ATTR_SET_SUBSCRIPTION;
        $defaultAttributeSetName = 'default';

        $this->dongleAttrSetId = $this->getAttributeSetIdByName($dongleAttributeSetName);
        $this->deviceAttrSetId = $this->getAttributeSetIdByName($deviceAttributeSetName);
        $this->defaultAttrSetId = $this->getAttributeSetIdByName($defaultAttributeSetName);
        $this->subscriptionAttrSetId = $this->getAttributeSetIdByName($subscriptionAttributeSetName);
    }

    public function getAttributeSetIdByName($attributeSetName)
    {
        $attributeSetModel = Mage::getModel('eav/entity_attribute_set');
        return $attributeSetModel->load($attributeSetName, 'attribute_set_name')->getAttributeSetId();
    }

    public function testisDongle()
    {
        $product = Mage::getModel('catalog/product');

        $resultForNull = $this->helper->isDongle();
        $this->assertFalse($resultForNull, 'isDongle(NULL) called');

        $resultForEmptyProduct = $this->helper->isDongle($product);
        $this->assertFalse($resultForEmptyProduct, 'isDongle(empty_product_object) called');

        $resultForDongleAttributeSetId = $this->helper->isDongle($this->dongleAttrSetId);
        $this->assertTrue($resultForDongleAttributeSetId, 'isDongle(dongle_attribute_set_id) called');

        $resultForDeviceAttributeSetId = $this->helper->isDongle($this->deviceAttrSetId);
        $this->assertFalse($resultForDeviceAttributeSetId, 'isDongle(not_dongle_attribute_set_id) called');

        $product->setAttributeSetId($this->defaultAttrSetId);
        $resultForDefaultAttributeSetId = $this->helper->isDongle($product);
        $this->assertFalse($resultForDefaultAttributeSetId, 'isDongle(not_dongle_product) called');

        $product->setAttributeSetId($this->dongleAttrSetId);
        $resultForDongle = $this->helper->isDongle($product);
        $this->assertTrue($resultForDongle, 'isDongle(dongle) called');
    }

    public function testisDevice()
    {
        $product = Mage::getModel('catalog/product');

        $resultForNull = $this->helper->isDevice();
        $this->assertFalse($resultForNull, 'isDevice(NULL) called');

        $resultForEmptyProduct = $this->helper->isDevice($product);
        $this->assertFalse($resultForEmptyProduct, 'isDevice(empty_product_object) called');

        $resultForDongleAttributeSetId = $this->helper->isDevice($this->dongleAttrSetId);
        $this->assertFalse($resultForDongleAttributeSetId, 'isDevice(dongle_attribute_set_id) called');

        $resultForDeviceAttributeSetId = $this->helper->isDevice($this->deviceAttrSetId);
        $this->assertTrue($resultForDeviceAttributeSetId, 'isDevice(device_attribute_set_id) called');

        $product->setAttributeSetId($this->defaultAttrSetId);
        $resultForDefaultAttributeSet = $this->helper->isDevice($product);
        $this->assertFalse($resultForDefaultAttributeSet, 'isDevice(not_device_product) called');

        $product->setAttributeSetId($this->deviceAttrSetId);
        $resultForDevice = $this->helper->isDevice($product);
        $this->assertTrue($resultForDevice, 'isDevice(device) called');
    }

    public function testisDeviceOrDongle()
    {
        $product = Mage::getModel('catalog/product');

        $resultForNull = $this->helper->isDeviceOrDongle();
        $this->assertFalse($resultForNull, 'isDeviceOrDongle(NULL) called');

        $resultForEmptyProduct = $this->helper->isDeviceOrDongle($product);
        $this->assertFalse($resultForEmptyProduct, 'isDeviceOrDongle(empty_product_object) called');

        $resultForDongleAttributeSetId = $this->helper->isDeviceOrDongle($this->dongleAttrSetId);
        $this->assertTrue($resultForDongleAttributeSetId, 'isDeviceOrDongle(dongle_attribute_set_id) called');

        $resultForDeviceAttributeSetId = $this->helper->isDeviceOrDongle($this->deviceAttrSetId);
        $this->assertTrue($resultForDeviceAttributeSetId, 'isDeviceOrDongle(device_attribute_set_id) called');

        $product->setAttributeSetId($this->defaultAttrSetId);
        $resultForDefaultAttributeSet = $this->helper->isDeviceOrDongle($product);
        $this->assertFalse($resultForDefaultAttributeSet, 'isDeviceOrDongle(not_device_or_dongle_product) called');

        $product->setAttributeSetId($this->dongleAttrSetId);
        $resultForDongle = $this->helper->isDongle($product);
        $this->assertTrue($resultForDongle, 'isDeviceOrDongle(dongle) called');

        $product->setAttributeSetId($this->deviceAttrSetId);
        $resultForDevice = $this->helper->isDeviceOrDongle($product);
        $this->assertTrue($resultForDevice, 'isDeviceOrDongle(device) called');
    }

    public function testisSubscription()
    {
        $product = Mage::getModel('catalog/product');

        $resultForNull = $this->helper->isSubscription();
        $this->assertFalse($resultForNull, 'isSubscription(NULL) called');

        $resultForEmptyProduct = $this->helper->isSubscription($product);
        $this->assertFalse($resultForEmptyProduct, 'isSubscription(empty_product_object) called');

        $resultForDongleAttributeSetId = $this->helper->isSubscription($this->dongleAttrSetId);
        $this->assertFalse($resultForDongleAttributeSetId, 'isSubscription(dongle_attribute_set_id) called');

        $resultForDeviceAttributeSetId = $this->helper->isDeviceOrDongle($this->deviceAttrSetId);
        $this->assertTrue($resultForDeviceAttributeSetId, 'isSubscription(device_attribute_set_id) called');

        $resultForSubscriptionAttributeSetId = $this->helper->isSubscription($this->subscriptionAttrSetId);
        $this->assertTrue($resultForSubscriptionAttributeSetId, 'isSubscription(subscription_attribute_set_id) called');

        $product->setAttributeSetId($this->defaultAttrSetId);
        $resultForDefaultAttributeSet = $this->helper->isSubscription($product);
        $this->assertFalse($resultForDefaultAttributeSet, 'isSubscription(not_subscription_product) called');

        $product->setAttributeSetId($this->subscriptionAttrSetId);
        $resultForDevice = $this->helper->isSubscription($product);
        $this->assertTrue($resultForDevice, 'isSubscription(subscription) called');
    }
}