<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Model_Feed extends Mage_Core_Model_Abstract
{   
    protected $_eventPrefix = 'adform_track_feed';
    
    private $_helper = null;
    
    protected function _construct()
    {
        $this->_init('adform_track/feed');
        $this->_helper = Mage::helper('adform_track');
    }
}
