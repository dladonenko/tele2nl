<?php
class Togo_Test extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Mage::app('default');
    }
    /*
    protected function _getQuoteMock()
    {
        foreach(Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item) {
            Mage::getSingleton('checkout/cart')->removeItem($item->getId());
        }
        Mage::getSingleton('checkout/cart')->save();
        //Mage::getSingleton('checkout/session')->clear();
        
        $fmcgCategory = Mage::getModel('catalog/category')->loadByAttribute('code', 'fmcg');

        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
			->load(Tele2_Install_Helper_Data::ATTR_SET_DEVICE, 'attribute_set_name')
			->getAttributeSetId();
        $_devices = Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
                ->addFieldToFilter('type_id', 'configurable')
                ->addCategoryFilter($fmcgCategory, true)
                ->addAttributeToFilter('status', '1')
                ->addAttributeToFilter('visibility', array('neq' => 1))
                ->setPageSize(1)
                ->load();
        $_device1 = $_devices->getFirstItem();
        if ($_device1->getEntityId()) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $productModel = Mage::getModel('catalog/product');
            $productObj = $productModel->setStoreId($storeId)->load($_device1->getEntityId());
            $productObj->setSkipCheckRequiredOption(true);
            $associatedProducts = $productObj->getTypeInstance()->getUsedProducts();
            foreach ($associatedProducts as $associatedProduct) {
                break;
            }
            $params = new Varien_Object();
            foreach ($productObj->getOptions() as $option) {
                if ($option->getDefaultTitle() == Tele2_Install_Helper_Data::CUSTOM_OPTION_SUBSCRIPTIONS) {
                    foreach ($option->getValues() as $value) {
                        break;
                    }
                }
            }
            $super_attribute = Mage::getModel('eav/entity_attribute')->load('color','attribute_code');
            $params->setData(array(
                'super_attribute' => Array(
                    $super_attribute->getAttributeId() => $associatedProduct->getColor()
                ),
                'options' => Array(
                    $option->getId() => $value->getId()
                ),
                'radioActivationType' => 'new',
                'newnumber' => '0712345678'
            ));
            //print_r($params->getData());
            $cart = Mage::getModel('checkout/cart');
            $cart->init();
            $cart->addProduct($productObj, $params);
            $cart->save();

            return Mage::getSingleton('checkout/session')->getQuote();
        }
    }
    
    public function testGetIsFmcgOnly()
    {
        $quote = $this->_getQuoteMock();
        $cart = Mage::getModel('tele4G_checkout/cart');
        $cart->setQuote($quote);
        $result = $cart->getIsFmcgOnly();
        $this->assertTrue($result);
    }
   */
    
    public function testTogoResellersBlock()
    {
        $block = Mage::app()->getLayout()->createBlock('tele4G_checkout/onepage_shippingmethods_resellertogo');
        if ($block instanceof Tele4G_Checkout_Block_Onepage_Shippingmethods_Resellertogo) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function testFormatHours()
    {
        $time = array(
            0 => "1970-01-01T14:15:00.978+01:00",
            1 => "1970-01-01T14.15:00.978+01:00",
            2 => "1970-01-01T1:15:00.978+01:00",
            3 => "1970-01-01T1415:00.978+01:00",
            4 => "1970-01-01",
            5 => "",
        );
        $togoHelper = Mage::helper("tele4G_togo");;
        $result = $togoHelper->formatHours($time[0]);
        if ($result == '14.15') {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        $result = $togoHelper->formatHours($time[1]);
        if ($result == '14.15') {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        $result = $togoHelper->formatHours($time[2]);
        if ($result == '1.15') {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        $result = $togoHelper->formatHours($time[3]);
        if (empty($result)) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        $result = $togoHelper->formatHours($time[4]);
        if (empty($result)) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        $result = $togoHelper->formatHours($time[5]);
        if (empty($result)) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }
}