<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */


class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();

        /**
        $model = Mage::registry('subscription');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

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
                'name' => 'subscription_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('tele2_subscription')->__('Name'),
            'title'     => Mage::helper('tele2_subscription')->__('Name'),
            'required'  => true,
        ));
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
        */
    }


}
