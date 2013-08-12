<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_WebsiteRestriction
 */
/**
 * Restriction modes dictionary
 *
 */
class Tele2_WebsiteRestriction_Model_System_Config_Source_Modes
    extends Enterprise_WebsiteRestriction_Model_System_Config_Source_Modes
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array_merge(parent::toOptionArray(), array(
            array(
                'value' => Tele2_WebsiteRestriction_Model_Mode::ALLOW_COOKIE,
                'label' => Mage::helper('tele2_websiteRestriction')->__('Private Sales: Authentication Based on Cookie'),
            ),
            array(
                'value' => Tele2_WebsiteRestriction_Model_Mode::ALLOW_URL_REQUEST,
                'label' => Mage::helper('tele2_websiteRestriction')->__('Private Sales: Authentication Based on URL Request'),
            ),
            array(
                'value' => Tele2_WebsiteRestriction_Model_Mode::ALLOW_REFERRER_URL,
                'label' => Mage::helper('tele2_websiteRestriction')->__('Private Sales: Referrer URL'),
            ),
        ));
    }
}
