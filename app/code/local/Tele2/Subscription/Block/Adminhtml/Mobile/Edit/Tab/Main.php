<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('subscription');

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

        if ($model->getSubscriptionId()) {
            $fieldset->addField('subscription_id', 'hidden', array(
                'name' => 'data[subscription_id]',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'data[name]',
            'label'     => Mage::helper('tele2_subscription')->__('Name'),
            'title'     => Mage::helper('tele2_subscription')->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => Mage::helper('tele2_subscription')->__('Image'),
            'title'     => Mage::helper('tele2_subscription')->__('Image'),
            'required'  => false,
        ));

        $fieldset->addField('subsidy_price', 'text', array(
            'name'      => 'data[subsidy_price]',
            'label'     => Mage::helper('tele2_subscription')->__('Subsidy Price'),
            'title'     => Mage::helper('tele2_subscription')->__('Subsidy Price'),
            'required'  => true,
        ));

        $fieldset->addField('subtitle', 'text', array(
            'name'      => 'data[subtitle]',
            'label'     => Mage::helper('tele2_subscription')->__('Subtitle'),
            'title'     => Mage::helper('tele2_subscription')->__('Subtitle'),
            'required'  => false,
        ));

        $fieldset->addField('short_description', 'textarea', array(
            'name'      => 'data[short_description]',
            'label'     => Mage::helper('tele2_subscription')->__('Short Description'),
            'title'     => Mage::helper('tele2_subscription')->__('Short Description'),
            'required'  => false,
        ));

        $fieldset->addField('usp', 'textarea', array(
            'name'      => 'data[usp]',
            'label'     => Mage::helper('tele2_subscription')->__('USP'),
            'title'     => Mage::helper('tele2_subscription')->__('USP'),
            'required'  => false,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'data[description]',
            'label'     => Mage::helper('tele2_subscription')->__('Description'),
            'title'     => Mage::helper('tele2_subscription')->__(' Description'),
            'required'  => false,
        ));

        $fieldset->addField('priceplan', 'text', array(
            'name'      => 'data[priceplan]',
            'label'     => Mage::helper('tele2_subscription')->__('Price Plan Code'),
            'title'     => Mage::helper('tele2_subscription')->__('Price Plan Code'),
            'required'  => false,
        ));

        $fieldset->addField('price', 'text', array(
            'name'      => 'data[price]',
            'label'     => Mage::helper('tele2_subscription')->__('Monthly Price'),
            'title'     => Mage::helper('tele2_subscription')->__('Monthly Price'),
            'required'  => true,
        ));

        $fieldset->addField('up_front_price', 'text', array(
            'name'      => 'data[up_front_price]',
            'label'     => Mage::helper('tele2_subscription')->__('UpFront Price'),
            'title'     => Mage::helper('tele2_subscription')->__('Up Front Price'),
            'required'  => false,
        ));

        $type1 = Tele2_Subscription_Model_Mobile::getType1Options();

        $type2 = Tele2_Subscription_Model_Mobile::getType2Options();

        $downgrade = Tele2_Subscription_Model_Mobile::getDowngradeOptions();

        $fieldset->addField('subscription_group', 'select', array(
            'name'      => 'data[subscription_group]',
            'label'     => Mage::helper('tele2_subscription')->__('Subscription Group'),
            'title'     => Mage::helper('tele2_subscription')->__('Subscription Group'),
            'values'    => $this->_getSubscriptionGroups(),
            'required'  => false,
        ));
        
        $fieldset->addField('type1', 'select', array(
            'name'      => 'data[type1]',
            'label'     => Mage::helper('tele2_subscription')->__('Type 1'),
            'title'     => Mage::helper('tele2_subscription')->__('Type 1'),
            'values'    => Mage::getModel('tele2_subscription/mobile')->getType1Options(),
            'required'  => false,
        ));

        $fieldset->addField('type2', 'select', array(
            'name'      => 'data[type2]',
            'label'     => Mage::helper('tele2_subscription')->__('Type 2'),
            'title'     => Mage::helper('tele2_subscription')->__('Type 2'),
            'values'    => $type2,
            'required'  => false,
        ));

        $fieldset->addField('downgrade', 'select', array(
            'name'      => 'data[downgrade]',
            'label'     => Mage::helper('tele2_subscription')->__('Downgrade'),
            'title'     => Mage::helper('tele2_subscription')->__('Downgrade'),
            'values'    => $downgrade,
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
    
    protected function _getSubscriptionGroups()
    {
        $aGroups = array();
        $aCollection = Mage::getModel('tele2_subscription/group')->getCollection();
        foreach ($aCollection as $group) {
            $aGroups[] = array('value' => $group['group_id'], 'label' => $group['name']);
        }
        return $aGroups;
    }
}
