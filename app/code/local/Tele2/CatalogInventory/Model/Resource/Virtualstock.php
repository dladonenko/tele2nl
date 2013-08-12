<?php
class Tele2_CatalogInventory_Model_Resource_Virtualstock extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tele2_cataloginventory/virtualstock', 'virtualstock_id');
    }
}
