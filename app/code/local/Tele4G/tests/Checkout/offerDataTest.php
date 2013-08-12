<?php
require_once "activationMock.php";

class offerDataTest extends PHPUnit_Framework_TestCase 
{
    protected $_subscription;
    protected $_product;
    protected $_productId;
    protected $_offerData = array();
    protected $_data = array();
    protected $_mock;

    public function setUp()
    {
        Mage::app('default');
        $this->offer = Mage::getModel('tele4G_checkout/offer');
        Mage::app()->getRequest()->setParam('newnumber', '0704051948');
        Mage::app()->getRequest()->setParam('sim_type', 'MINI_REGULAR');
        Mage::app()->getRequest()->setParam('radioSimNotNeed', 1);
        
        $this->_subscription = $this->_mockSubscription();
        $this->_product = $this->_mockProduct();
        $this->_mock = new Activation_Mock();
    }
    
    protected function _getQuoteMock($array)
    {
        $deviceAttributeSetId = Mage::getModel('eav/entity_attribute_set')
			->load(Tele2_Install_Helper_Data::ATTR_SET_DEVICE, 'attribute_set_name')
			->getAttributeSetId();
        
        $_devices = Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('attribute_set_id', $deviceAttributeSetId)
                ->addFieldToFilter('type_id', 'simple')
                ->addAttributeToFilter('status', '1')
                ->addAttributeToFilter('visibility', array('neq' => 1))
                ->setPageSize(1)
                ->load();
        $_device1 = $_devices->getFirstItem();
        
        $storeId = Mage::app()->getStore()->getStoreId();
        $quoteObj = Mage::getModel('sales/quote');
        $storeObj = $quoteObj->getStore()->load($storeId);
        $quoteObj->setStore($storeObj);
        $productModel = Mage::getModel('catalog/product');
        $this->_productId = $_device1->getId();
        $productObj = $productModel->setStore($storeId)->setStoreId($storeId)->load($this->_productId);
        $productObj->setSkipCheckRequiredOption(true);
        $quoteItem = $quoteObj->addProduct($productObj);
        foreach ($array as $key => $value) {
            $quoteItem->setData($key, $value);
        }
        $quoteItem->setQuote($quoteObj);                                    
        $quoteObj->addItem($quoteItem);
        
        return $quoteObj;
    }
    
    protected function _mockSubscription()
    {
        return Mage::getModel('tele2_subscription/mobile');
    }

    protected function _mockProduct()
    {
        return Mage::getModel('catalog/product');
    }

    public function testSaveOfferDataForNewNumber()
    {
        Mage::app()->getRequest()->setParam('radioActivationType', 'new');
        $sim_type = Mage::app()->getRequest()->getParam('sim_type');
        $this->_mock->setSimType($sim_type);
        
        $radioActivationType = Mage::app()->getRequest()->getParam('radioActivationType');

        $this->_subscription->setType1(Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST);

        if ($radioActivationType == "new") {
            $this->offer->setActivationTypeNew($this->_subscription);
        }

        $this->_offerData[$this->_mock->getOfferId()]['sim_type'] = $this->_mock->getSimType();
        
        if ($this->offer->getActivationType()) {
            $this->_offerData[$this->_mock->getOfferId()]['type'] = $this->offer->getActivationType();
        }
        if ($this->offer->getActivationNumber()) {
            $this->_offerData[$this->_mock->getOfferId()]['number'] = $this->offer->getActivationNumber();
        }
        $this->assertArrayHasKey($this->_mock->getOfferId(), $this->_offerData);
        $this->assertEquals($this->_offerData[$this->_mock->getOfferId()]['sim_type'], $this->_mock->getSimType());
        $this->assertEquals($this->_offerData[$this->_mock->getOfferId()]['type'], $this->offer->getActivationType());
        $this->assertEquals($this->_offerData[$this->_mock->getOfferId()]['number'], $this->offer->getActivationNumber());
    }
    
    public function testGetProductOfferFromQuote()
    {
        
        $dataToMock = array(
            'article_id' => 15935745,
            'offer_id' => 98756,
        );
        $quote = $this->_getQuoteMock($dataToMock);
        $this->offer->setQuote($quote);
        $session = Mage::getSingleton('checkout/session');
        $session->setOfferId($dataToMock['offer_id']);
        $session->setOfferParamsAfterCart(array('product' => $this->_productId));
        $productOffer = $this->offer->getProductOfferFromQuote();
        if (is_object($productOffer)) {
            $this->assertEquals($productOffer->getArticleId(), $dataToMock['article_id']);
        } else {
            $this->assertTrue(false);
        }
    }
}