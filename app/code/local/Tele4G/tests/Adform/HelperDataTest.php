<?php
class Tele4G_Adform_Helper_DataTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Mage::app('default');
        $this->_helper = Mage::helper('tele4G_adform');
    }
    
    public function testGetAge()
    {
        $age = $this->_helper->getAge($this->getRealSnn());
        $this->assertInternalType('integer', $age);
        $this->assertNotEmpty($age);
    }
    
    public function testGetAgeGroup()
    {
        $ageGroup = $this->_helper->getAgeGroup($this->getRealSnn());
        $this->assertInternalType('string', $ageGroup);
        $this->assertNotEmpty($ageGroup);
    }
    
    public function testGetGenderFromSsn()
    {
        $gender = $this->_helper->getGenderFromSsn($this->getRealSnn());
        $this->assertInternalType('string', $gender);
        $this->assertNotEmpty($gender);
    }

    protected function getRealSnn()
    {
        $arraySsn = array('198609050417','197002240468','197404090230','198403103610');
        shuffle($arraySsn);
        return array_shift($arraySsn);
    }
}
