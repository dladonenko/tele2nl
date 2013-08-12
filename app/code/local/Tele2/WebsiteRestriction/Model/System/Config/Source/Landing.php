<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_WebsiteRestriction
 */

/**
 * Sys config source model for private sales redirect modes
 *
 */
class Tele2_WebsiteRestriction_Model_System_Config_Source_Landing
extends Varien_Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array_merge(
            Mage::getModel('adminhtml/system_config_source_cms_page')->toOptionArray(),
            array(
                array(
                    'value' => 'root',
                    'label' => Mage::helper('enterprise_websiterestriction')->__('Main Website'),
                )
            )
        );
    }
}
