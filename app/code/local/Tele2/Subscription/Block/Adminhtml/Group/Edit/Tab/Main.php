<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Block_Adminhtml_Group_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('subscription_group');

        $form = new Varien_Data_Form();

        //array('id' => 'edit_form2', 'action' => $this->getData('action'), 'method' => 'post')
        //);

        //$form->setHtmlIdPrefix('_');

        $fieldset = $form->addFieldset(
            'base_fieldset', 
            array(
                'legend' => Mage::helper('tele2_subscription')->__('Manage fields'), 
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getGroupId()) {
            $fieldset->addField('group_id', 'hidden', array(
                'name' => 'group_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('tele2_subscription')->__('Name'),
            'title'     => Mage::helper('tele2_subscription')->__('Name'),
            'required'  => true,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();

    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('General');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
