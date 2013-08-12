<?php

/**
 * Used in creating options for Downgrade errors value selection
 *
 */
class Tele4G_Subscription_Model_Downgrade_Errors
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 'CREDIT_CONTROL_REJECTED', 'label'=>Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED')),
            array('value' => 'CREDIT_CHECK_FIRST_LEVEL_VALID', 'label'=>Mage::helper('adminhtml')->__('CREDIT_CHECK_FIRST_LEVEL_VALID')),
            array('value' => 'CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_0_NOT_SUFFICIENT', 'label'=>Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_0_NOT_SUFFICIENT')),
            array('value' => 'CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_1_NOT_SUFFICIENT', 'label'=>Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_1_NOT_SUFFICIENT')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('No'),
            'CREDIT_CONTROL_REJECTED' => Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED'),
            'CREDIT_CHECK_FIRST_LEVEL_VALID' => Mage::helper('adminhtml')->__('CREDIT_CHECK_FIRST_LEVEL_VALID'),
            'CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_0_NOT_SUFFICIENT' => Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_0_NOT_SUFFICIENT'),
            'CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_1_NOT_SUFFICIENT' => Mage::helper('adminhtml')->__('CREDIT_CONTROL_REJECTED_LIMITED_APPROVAL_LEVEL_1_NOT_SUFFICIENT'),
        );
    }

}
