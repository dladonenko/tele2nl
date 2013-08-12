<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Model_Mysql4_Feed_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('adform_track/feed');
    }
}
