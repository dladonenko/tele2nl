<?php
/**
 * Magento Enterprise Edition
 * 
 * @category    Tele2
 * @package     Tele2_WebsiteRestriction
 */

/**
 * Private sales and stubs observer
 *
 */
class Tele2_PageCache_Model_Observer extends Enterprise_WebsiteRestriction_Model_Observer
{
    public function cleanCache()
    {
        $cacheToClear = array('block_html');
        Mage::log('clean cache method', null, 'clean_cache.log');

        try {
            $allTypes = Mage::app()->useCache();
            foreach($allTypes as $type => $cache) {
                if (in_array($type, $cacheToClear)) {
                    Mage::app()->getCacheInstance()->cleanType($type);
                    Mage::log("clean chached: {$type}", null, 'clean_block_cache.log');
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function cleanCacheAll()
    {
        Mage::log('clean all caches', null, 'clean_cache.log');

        try {
            $allTypes = Mage::app()->useCache();
            foreach($allTypes as $type => $cache) {
                Mage::app()->getCacheInstance()->cleanType($type);
                Mage::log("clean chached: {$type}", null, 'clean_all_cache.log');
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
