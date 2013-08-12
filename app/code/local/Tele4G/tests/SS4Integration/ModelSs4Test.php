<?php
class Tele4G_SS4Integration_Model_SS4IntegrationTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
       $this->ssn = Mage::getModel('tele4G_sS4Integration/sS4Integration');        
       $sales = Mage::getModel('sales/order')->getCollection()->setPageSize(1)->addAttributeToSort('created_at', 'DESC');
       $data = $sales->getData();
       $first = current($data);       
       $this->order = Mage::getModel('sales/order')->load($first['entity_id']);    
       $this->order->setAssistantData('testdata');       
       $this->updateMock = $this->getMock('Tele4G_SS4Integration_Model_SS4Integration', array('addCommentToOrder','sendRequestToSaveOrder'));        
       $this->updateMock->expects($this->any())
                ->method('addCommentToOrder')
                ->will($this->returnValue(TRUE));       
       $this->updateMock->expects($this->any())
                ->method('sendRequestToSaveOrder')
                ->will($this->returnCallback(array($this, 'testResponse')));
    }
    
    public function testResponse(){
        return '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><soap:Fault><faultcode>soap:Server</faultcode><faultstring>Magento order id 1300014961 already used!</faultstring></soap:Fault></soap:Body></soap:Envelope>';
    }
    /*
    public function testcreatOrder()
    {        
       $response = $this->updateMock->creatOrder($this->order);                
       $this->assertNotNull($response);
    }
    */
    public function testisPossibleToSendAsLetter()
    {
        $ispossible = $this->updateMock->isPossibleToSendAsLetter($this->order); 
        $this->assertInternalType(
            "bool", $ispossible, 'WSDL return wrong format'
        );
    }  
}