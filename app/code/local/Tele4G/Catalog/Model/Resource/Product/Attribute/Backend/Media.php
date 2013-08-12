<?php
/**
 * Catalog product media gallery attribute backend resource
 * - added posibility to load image gallery from child product to configurable
 *
 * @category    Conviq
 * @package     Conviq_Catalog
 */
class tele4G_Catalog_Model_Resource_Product_Attribute_Backend_Media 
    extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
{
    /**
     * Load gallery images for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Product_Attribute_Backend_Media $object
     * @return array
     */
    public function loadGallery($product, $object)
    {
        if (!$product->isConfigurable()) {
            return parent::loadGallery($product, $object);
        }

        $adapter = $this->_getReadAdapter();

        $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

        // Select gallery images for product
        $select = $adapter->select()
            ->from(
                array('main'=>$this->getMainTable()),
                array('entity_id', 'value_id', 'value AS file')
            )
            ->joinLeft(
                array('value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$product->getStoreId()),
                array('label','position','disabled')
            )
            ->joinLeft( // Joining default values
                array('default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                'main.value_id = default_value.value_id AND default_value.store_id = 0',
                array(
                    'label_default' => 'label',
                    'position_default' => 'position',
                    'disabled_default' => 'disabled'
                )
            )
            ->where('main.attribute_id = ?', $object->getAttribute()->getId())
            ->where('main.entity_id = ?', $product->getId())
            ->order('main.entity_id ' . Varien_Db_Select::SQL_ASC)
            ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);

        // Add images from children products
        $productChildren = $product->getTypeInstance()->getChildrenIds($product->getId());
        if (count($productChildren[0])) {
            foreach($productChildren[0] as $productChildId)
            $select
                ->orWhere('main.entity_id = ?', $productChildId);
        }

        $result = $adapter->fetchAll($select);
        $this->_removeDuplicates($result);
        return $result;
    }
}
