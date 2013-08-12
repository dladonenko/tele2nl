<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Lanot_FileManager_Model_Resource_FileProduct_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('lanot_filemanager/fileProduct');
    }

    /**
     * Get collection using file info
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function deleteByProduct($product)
    {
        if ($product->getId()) {
            $select = $this->getSelect();
            $select->where('product_id = ?', $product->getId());
            foreach ($this as $fileProduct) {
                $fileProduct->delete();
            }
        }
        return $this;
    }
}
