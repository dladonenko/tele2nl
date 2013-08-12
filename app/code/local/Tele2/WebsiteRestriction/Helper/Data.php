<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_WebsiteRestriction
 */

/**
 * WebsiteRestriction helper for translations
 *
 */
class Tele2_WebsiteRestriction_Helper_Data extends Enterprise_WebsiteRestriction_Helper_Data
{
    /**
     * Website restriction settings
     */
    const XML_PATH_RESTRICTION_COOKIE_NAME        = 'general/restriction/cookie_name';
    const XML_PATH_RESTRICTION_COOKIE_VALUE       = 'general/restriction/cookie_value';
    const XML_PATH_RESTRICTION_COOKIE_CHECK_VALUE = 'general/restriction/cookie_check_value';

    /**
     * Define if restriction is active
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function getIsRestrictionEnabled($store = null)
    {
        return (bool)(int)Mage::getStoreConfig(self::XML_PATH_RESTRICTION_ENABLED, $store);
    }
}
