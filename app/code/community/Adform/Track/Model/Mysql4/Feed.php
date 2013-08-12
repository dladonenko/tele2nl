<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Model_Mysql4_Feed extends Mage_Core_Model_Mysql4_Abstract
{   
    protected function _construct()
    {
        $this->_init('adform_track/feed', 'feed_id');
    }
}
