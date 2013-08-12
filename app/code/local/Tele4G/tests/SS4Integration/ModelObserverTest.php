<?php
class Tele4G_SS4Integration_Model_ObserverTest extends PHPUnit_Framework_TestCase {
     
    
    public function setUp()
    {
        $this->ssn = Mage::getModel('tele4G_sS4Integration/sS4Integration');
        $this->ssnObserver = Mage::getModel('tele4G_sS4Integration/observer');
        
        $this->updateMock = $this->getMock('Tele4G_SS4Integration_Model_Observer', array('updateProduct','getProductStock'));        
        $this->updateMock->expects($this->any())
                ->method('updateProduct')
                ->will($this->returnValue(TRUE));
        
    }
    public function testupdatePopularDevices()
    {        
        $popularDevice = $this->updateMock->updatePopularDevices(); 
        $this->assertInstanceOf('Tele4G_SS4Integration_Model_Observer',
            $popularDevice,
            'Method returns wrong object');
        $this->assertNotEmpty($popularDevice,'processing error');
    }
    
    public function testupdateAllDevices()
    {        
            $allDevice = $this->updateMock->updateAllDevices(); 
            $this->assertInstanceOf('Tele4G_SS4Integration_Model_Observer',
                $allDevice,
                'Method returns wrong object');
            $this->assertNotEmpty($allDevice,'processing error');
    }
    
    
    public function testupdateAccessories()
    {        
            $updateAccessories = $this->updateMock->updateAccessories(); 
            $this->assertInstanceOf('Tele4G_SS4Integration_Model_Observer',
                $updateAccessories,
                'Method returns wrong object');
            $this->assertNotEmpty($updateAccessories,'processing error');
    }
    /*
    public function testgetProductStock()
    {       
        $articleId = '1';
        $stock = $this->ssnObserver->getProductStock($articleId);
        $this->assertEmpty($stock);        
    }
    */
    public function testgetProducts()
    {       
        $products = $this->ssnObserver->getProducts();       
        $this->assertLessThanOrEqual($products->count(),0,'no products');
    }
    
    public function testgetBestsellers()
    {       
        $bestsleers = $this->ssnObserver->getBestsellers();       
        $this->assertNotEmpty($bestsleers,'no bestsellrs products');
    }
    
    public function testgetMostviewed()
    {       
        $mostviewed = $this->ssnObserver->getMostviewed();        
        $this->assertNotEmpty($mostviewed,'no mostviewed products');
    }
    
    public function testgetStoreId()
    {       
        $storeId = $this->ssnObserver->getStoreId();       
        $this->assertInternalType("numeric",$storeId,'return wrong type format');        
    }
    
    public function testswitchIndexerMode()
    {       
        $this->ssnObserver->switchIndexerMode(Mage_Index_Model_Process::MODE_REAL_TIME);       
        $pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($pCollection as $process) {            
            $currentMode = $process->getMode();            
            $this->assertEquals('real_time',$currentMode,'incorrect mode');
        }        
    }
}