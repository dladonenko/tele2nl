<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Block_Adminhtml_Config_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('subscription_config');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'base_fieldset', 
            array(
                'legend' => Mage::helper('tele2_subscription')->__('Manage fields'), 
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getConfigId()) {
            $fieldset->addField('config_id', 'hidden', array(
                'name' => 'data[config_id]',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'data[name]',
            'label'     => Mage::helper('tele2_subscription')->__('Name'),
            'title'     => Mage::helper('tele2_subscription')->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'data[description]',
            'label'     => Mage::helper('tele2_subscription')->__('Description'),
            'title'     => Mage::helper('tele2_subscription')->__('Description'),
            'required'  => false,
        ));

        $fieldset->addField('image_main', 'image', array(
            'name'      => 'image_main',
            'label'     => Mage::helper('tele2_subscription')->__('Image'),
            'title'     => Mage::helper('tele2_subscription')->__('Image'),
            'required'  => false,
        ));
        
        $fieldset->addField('display_in_cart', 'select', array(
            'name'      => 'data[display_in_cart]',
            'label'     => Mage::helper('tele2_subscription')->__('Display in cart'),
            'title'     => Mage::helper('tele2_subscription')->__('Display in cart'),
            'required'  => false,
            'values'    => Mage::getModel("adminhtml/system_config_source_yesno")->toOptionArray(),
        ));

        $fieldset->addField('article_id', 'text', array(
            'name'      => 'data[article_id]',
            'label'     => Mage::helper('tele2_subscription')->__('Article Id'),
            'title'     => Mage::helper('tele2_subscription')->__('Article Id'),
            'required'  => false,
        ));

        $fieldset->addField('priceplan', 'text', array(
            'name'      => 'data[priceplan]',
            'label'     => Mage::helper('tele2_subscription')->__('Price Plan Code'),
            'title'     => Mage::helper('tele2_subscription')->__('Price Plan Code'),
            'required'  => false,
        ));

        $fieldset->addField('price_with_vat', 'text', array(
            'name'      => 'data[price_with_vat]',
            'label'     => Mage::helper('tele2_subscription')->__('Price With VAT'),
            'title'     => Mage::helper('tele2_subscription')->__('Price With VAT'),
            'required'  => false,
        ));

        $fieldset->addField('price_without_vat', 'text', array(
            'name'      => 'data[price_without_vat]',
            'label'     => Mage::helper('tele2_subscription')->__('Price Without VAT'),
            'title'     => Mage::helper('tele2_subscription')->__('Price Without VAT'),
            'required'  => false,
        ));

        $fieldset->addField('fee_with_vat', 'text', array(
            'name'      => 'data[fee_with_vat]',
            'label'     => Mage::helper('tele2_subscription')->__('Bonus With VAT'),
            'title'     => Mage::helper('tele2_subscription')->__('Bonus With VAT'),
            'required'  => false,
        ));

        $fieldset->addField('fee_without_vat', 'text', array(
            'name'      => 'data[fee_without_vat]',
            'label'     => Mage::helper('tele2_subscription')->__('Bonus Without VAT'),
            'title'     => Mage::helper('tele2_subscription')->__('Bonus Without VAT'),
            'required'  => false,
        ));

        $fieldset->addField('month', 'text', array(
            'name'      => 'data[month]',
            'label'     => Mage::helper('tele2_subscription')->__('Month'),
            'title'     => Mage::helper('tele2_subscription')->__('Month'),
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
