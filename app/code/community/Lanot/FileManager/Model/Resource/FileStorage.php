<?php
/**
 * Lanot filemanager module
 *
 * @category    Lanot
 * @package     Lanot_FileManager
 */
class Lanot_FileManager_Model_Resource_FileStorage extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('lanot_filemanager/file_storage', 'file_id');
    }
}
