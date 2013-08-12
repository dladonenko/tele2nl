<?php
class Tele2_FreeGifts_installTest extends PHPUnit_Framework_TestCase {

    protected $_installer;
    protected $_connection;

    public function setUp()
    {
        Mage::app('default');
        $this->_installer = Mage::getResourceModel('core/setup', 'read');
        $this->_connection = $this->_installer->getConnection();
    }

    public function testInstallTable()
    {
        $this->assertTrue($this->_connection->isTableExists($this->_installer->getTable('tele2_freegift')));
    }

}
