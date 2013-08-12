<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Block_Adminhtml_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
		parent::__construct();

        $this->_objectId   = 'group_id';
        $this->_blockGroup = 'tele2_subscription';
        $this->_controller = 'adminhtml_group';

		$this->_updateButton('save', 'label', Mage::helper('tele2_subscription')->__('Save Group'));
        $group_id = $this->getRequest()->getParam('group_id');
        if ($group_id) {
            $this->_addButton('save_and_edit_button', array(
                'label'     => Mage::helper('tele2_subscription')->__('Delete'),
                'onclick'   => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', array('group_id' => $this->getRequest()->getParam('group_id'))) . '\')',
                'class' => 'delete'
            ), 1);
        }
        
        $this->_removeButton('back');
    }

    public function getFormActionUrl() 
    {
        return $this->getUrl('*/*/save');
    }

    public function getHeaderText()
    {
		if( Mage::registry('subscription_group') && Mage::registry('subscription_group')->getId() ) {
            return Mage::helper('tele2_subscription')->__("Edit Group '%s'", $this->htmlEscape(Mage::registry('subscription_group')->getName()));
        } else {
            return Mage::helper('tele2_subscription')->__('Add Group');
        }
    }
    
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            //'active_tab' => '{{tab_id}}'
        ));
    }
}
