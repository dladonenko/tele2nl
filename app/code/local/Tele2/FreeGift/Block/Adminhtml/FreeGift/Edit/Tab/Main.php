<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_FreeGift_Block_Adminhtml_FreeGift_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $model = Mage::registry('freeGift');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'base_fieldset', 
            array(
                'legend' => Mage::helper('tele2_freeGift')->__('Manage fields'), 
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'data[entity_id]',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'data[name]',
            'label'     => Mage::helper('tele2_freeGift')->__('Name'),
            'title'     => Mage::helper('tele2_freeGift')->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('coupon_code', 'text', array(
            'name'      => 'data[coupon_code]',
            'label'     => Mage::helper('tele2_freeGift')->__('Coupon Code'),
            'title'     => Mage::helper('tele2_freeGift')->__('Coupon Code'),
            'required'  => false,
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
