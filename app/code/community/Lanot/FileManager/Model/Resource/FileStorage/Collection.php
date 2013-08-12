<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Lanot_FileManager_Model_Resource_FileStorage_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('lanot_filemanager/fileStorage');
    }

    /**
     * Get collection using file info
     * @param array $pathInfo
     * @return $this
     */
    public function addByFileAttributes($pathInfo)
    {
        $select = $this->getSelect();
        $select->where('filename = ?', $pathInfo['basename'])
            ->where('directory = ?', $pathInfo['dirname'])
            ->where('type = ?', $pathInfo['extension']);
        return $this;
    }

    /**
     * Get collection related to product by id
     *
     * @param int $productId
     * @return $this
     */
    public function getFilesByProductId($productId = null)
    {
        $select = $this->getSelect();
        $select->joinInner(array('lfp' => 'lanot_file_product'), 'lfp.file_id=main_table.file_id');
        $select->where('lfp.product_id = ?', $productId);
        return $this;
    }
}
