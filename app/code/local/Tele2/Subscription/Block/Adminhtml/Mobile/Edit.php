<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Block_Adminhtml_Mobile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'subscription_id';
        $this->_blockGroup = 'tele2_subscription';
        $this->_controller = 'adminhtml_mobile';

        parent::__construct();

        $this->_addButton('save_and_edit_button', array(
            'label'     => Mage::helper('tele2_subscription')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save',
        ), 1);
        $this->_formScripts[] = 'function saveAndContinueEdit() {
            editForm.submit($(\'edit_form\').action + \'back/edit/\');}';
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('subscription')->getId()) {
            return Mage::helper('tele2_subscription')->__("Edit Mobile Subscription '%s'", $this->htmlEscape(Mage::registry('subscription')->getName()));
        }
        else {
            return Mage::helper('tele2_subscription')->__('New Mobile Subscription');
        }
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/mobileSave', array(
            '_current'   => true,
            'back'       => 'edit',
            //'active_tab' => '{{tab_id}}'
        ));
    }

    public function getFormActionUrl() {
        return $this->getUrl('*/*/mobileSave');
    }
}
