<?php
require_once "activationMock.php";

class activationNumberTest extends PHPUnit_Framework_TestCase 
{
    protected $_subscription;
    protected $_product;
    protected $_offerData = array();
    protected $_data = array();
    protected $_mock;

    public function setUp()
    {
        $this->offer = Mage::getModel('tele4G_checkout/offer');
        Mage::app()->getRequest()->setParam('newnumber', '0704051948');
        Mage::app()->getRequest()->setParam('sim_type', 'MINI_REGULAR');
        Mage::app()->getRequest()->setParam('radioSimNotNeed', 1);
        
        $this->_subscription = $this->_mockSubscription();
        $this->_product = $this->_mockProduct();
        $this->_mock = new Activation_Mock();
    }

    protected function _mockSubscription()
    {
        return Mage::getModel('tele2_subscription/mobile');
    }

    protected function _mockProduct()
    {
        return Mage::getModel('catalog/product');
    }
            
    public function testSetActivationTypeNew()
    {
        $this->_subscription->setType1(Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST);

        $this->assertTrue($this->offer->setActivationTypeNew($this->_subscription), "Error Activation Number");
    }
    
    public function testSetActivationTypeExist()
    {
        $checkout = Mage::getSingleton('checkout/session');
        $checkout->setActivationExistNumber('0704051948');
        $checkout->setActivationExistType('port');
        
        $this->assertTrue($this->offer->setActivationTypeExist(), "Error Exist Activation Number");
    }
    
    public function testSaveSimTypeForProlong()
    {
        $radioSimNotNeed = Mage::app()->getRequest()->getParam('radioSimNotNeed');
        $this->_mock->setActivationType('prolong');
        
        if ($radioSimNotNeed && strtolower($this->_mock->getActivationType()) == 'prolong') {
            $this->_offerData[$this->_mock->getOfferId()]['sim_type'] = null;
        }
        $this->assertNull($this->_offerData[$this->_mock->getOfferId()]['sim_type']);
    }

    public function testSaveSimTypeForSubscription()
    {
        $sim_type = Mage::app()->getRequest()->getParam('sim_type');
        $this->_mock->setSimType($sim_type);

        $this->_offerData[$this->_mock->getOfferId()]['sim_type'] = $this->_mock->getSimType();
        
        $this->assertEquals($this->_offerData[$this->_mock->getOfferId()]['sim_type'], $sim_type);
    }

    public function testSaveSimTypeForDevice()
    {
        $this->_product->setAttributeText('sim_type', 'COMBO');
        $this->_offerData[$this->_mock->getOfferId()]['sim_type'] = $this->_product->getAttributeText('sim_type');
        
        $this->assertEquals($this->_offerData[$this->_mock->getOfferId()]['sim_type'], $this->_product->getAttributeText('sim_type'));
    }
}