<?php
/**
 * Lanot File Manager module
 *
 * @category    Lanot
 * @package     Lanot_FileManager
 */


class Lanot_FileManager_Model_FileStorage extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('lanot_filemanager/fileStorage');
    }
}
