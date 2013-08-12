<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Grid_Container 
{
    public function __construct() {


        $this->_blockGroup = 'adform_track';
        $this->_controller = 'adminhtml_edit';


        $this->_headerText = Mage::helper('adminhtml')->__('Adform XML Product Feeds');

        parent::__construct();

        $this->_removeButton('add');
    }

    public function getSaveUrl() 
    {
        return $this->getUrl('*/*/save', array('store' => $this->getRequest()->getParam('store')));
    }
}
