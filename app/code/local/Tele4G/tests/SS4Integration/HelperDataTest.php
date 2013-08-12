<?php
require_once "TestSoapClient.php";

class Tele4G_SS4Integration_Helper_DataTest extends PHPUnit_Framework_TestCase {
    public $ssn;

    public function setUp()
    {
        $this->ssn = Mage::helper('tele4G_sS4Integration');
        $this->clientMock = $this->getMock('Tele4G_SS4Integration_Helper_Data', array('getClient'));
        $this->clientMock->expects($this->any())
              ->method('getClient')
              ->will($this->returnCallback(array($this, 'setSoapMock')));
    }
    
    public function setSoapMock(){
        return new TestSoapClient;
    }
    
    public function testgetSs4Result()
    {
         $sendChallange = $this->clientMock->getSs4Result('sendChallengeSms','0777777777'); 
         $this->assertNotNull(
                $sendChallange,
                'sendChallengeSms method return empty'
                );
         $this->assertEquals(
                'OK',
                (string)$sendChallange->sendChallengeSmsResponse->responseStatus->status,
                'sendChallengeSms method return wrong answer'
                );      
    }

    public function testsendChallengeSms()
    {
        $numberCorrect = '0777777777';
        $sendChallange = $this->clientMock->sendChallengeSms($numberCorrect);
        $this->assertNotNull($sendChallange, 'sendChallengeSms method return false');
        
        $statusCorrect = (string)$sendChallange->result->responseStatus->status;
        $codeCorrect   = (string)$sendChallange->result->responseStatus->errorCode;
        $this->assertEquals('OK',$statusCorrect,'send sms to number '.$numberCorrect.' return error - '.$codeCorrect);
        
    }
    
    public function testvalidateSmsCode()
    {
        $number = '0777777777';
        $code = '9999';
        $result = $this->clientMock->validateSmsCode($number,$code);         
        
        $this->assertNotNull($result, 'validateSmsCode method return empty');
        $this->assertInternalType(
           "string", $result->result->result, 'return wrong format, error - '.$result->result->result
        );
    }
    
    
   
    public function testgetATypeForExistNum()
    {
        $number = '0777777777';
        $code = 'QFL1';
        $avaible = $this->clientMock->getATypeForExistNum($number,$code);
        $activationType = $avaible->result->activationType;
        $this->assertNotEquals( 0, $activationType,'Activation type return 0' );
        $this->assertInternalType(
           "string", $activationType, 'return wrong format, error - '.$avaible->result->responseStatus->errorName
        );
    }    
    
    public function testgetStockLevel(){
        $articleId = '113136';
        $stocklevel = $this->clientMock->getStockLevel($articleId);
        $this->assertInternalType(
           "string", $stocklevel->stockLevels->responseStatus->status, 'stock level is not string!'
      );        
    }    
    
    
    
    public function testremoveChosenNumber(){
        //start testgetAvailablePhoneNumbers
        $count = 5;
        $numbers = $this->clientMock->getAvailablePhoneNumbers($count);
        $this->assertEquals($count, count($numbers),'is not the right number of numbers');      
        //end
        
        Mage::getSingleton('checkout/session')->setAvailablePhoneNumbers(serialize($this->ssn->getAvailablePhoneNumbers($count)));
        $availablePhoneNumbersCache = Mage::getSingleton('checkout/session')->getAvailablePhoneNumbers();
        $numbersBefore = unserialize($availablePhoneNumbersCache);
        $countBefore = count($numbersBefore);
        $first =  current($numbersBefore);
        $this->assertNotEmpty($first,'list of available phone numbers is empty');
        
        $this->ssn->removeChosenNumber($first);
        
        $availablePhoneNumbersCacheAfter = Mage::getSingleton('checkout/session')->getAvailablePhoneNumbers();
        $numbersAfter = unserialize($availablePhoneNumbersCacheAfter);
        $countAfter = count($numbersAfter);
        
        $this->assertNotEquals($countBefore, $countAfter,'chosen number didn\'t remove');
    }
    
    
    public function testgetClient()
    {   
        $wsdl = Mage::getStoreConfig('tele4G/ss4/wsdl');
        $this->assertNotNull($wsdl, 'WSDL to soap connect is empty');
        $this->assertInternalType(
           "string", $wsdl, 'WSDL return wrong format'
        );
    }
    
    
    public function testdecryptCookie()
    {
         $cookiesData = 'cookiesData';
         $decryptCookie = $this->ssn->decryptCookie($cookiesData);
         $this->assertNotNull(
                $decryptCookie,
                'decrypt Cookie return NULL'
                );
         $this->assertInternalType(
           "string", $decryptCookie, 'return wrong format'
        );
    }
    
    public function testgetInfoBySsn()
    {
        $ss4info = $this->clientMock->getInfoBySsn('194211016151');
        $statusCorrect = (string)$ss4info->individualResponse->responseStatus->status;
        $this->assertNotNull($ss4info, 'SSN info is empty');
        $this->assertEquals('OK',$statusCorrect,'Ssn info return wrong status');
    }

    public function testGetErrorFromXML()
    {
        $error = 'INVALID_EMAIL';
        $errorText = 'Den angivna epost-adressen är inte giltig';
        $errorResult1 = $this->ssn->getErrorFromXML($error);
        $this->assertInternalType('string', $errorResult1);
        $this->assertNotEmpty($errorResult1);
        $this->assertSame($errorText, $errorResult1);

        $errorFake = 'ABCDEFGH00123456789';
        $errorResult2 = $this->ssn->getErrorFromXML($errorFake);
        $this->assertSame($errorFake, $errorResult2);

        $errorEmpty = '';
        $errorResult3 = $this->ssn->getErrorFromXML($errorEmpty);
        $this->assertFalse($errorResult3);

        $errorNull = NULL;
        $errorResult4 = $this->ssn->getErrorFromXML($errorNull);
        $this->assertFalse($errorResult4);
    }    
}