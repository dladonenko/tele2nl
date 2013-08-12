<?php

class Tele2_Adminhtml_Block_Dashboard_Tab_Products_Outofstock extends Mage_Adminhtml_Block_Dashboard_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productsOutofstockGrid');
    }

    protected function _prepareCollection()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Catalog')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {

            $collection = Mage::helper('tele2_catalogInventory')->getOutOfStockProducts();
            $collection->addStoreFilter($storeId)->addAttributeToSelect('name');
            $collection->getSelect()->joinLeft(
                array('eas' => 'eav_attribute_set'),
                "e.attribute_set_id = eas.attribute_set_id",
                array('eas.attribute_set_name')
            );

            $this->setDefaultLimit(99);

            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'name'
        ));

        $this->addColumn('attribute_set_name', array(
            'header'    => Mage::helper('sales')->__('Attribute Set Name'),
            'sortable'  => false,
            'index'     => 'attribute_set_name'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /*
     * Returns row url to show in admin dashboard
     * $row is bestseller row wrapped in Product model
     *
     * @param Mage_Catalog_Model_Product $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        // getId() would return id of bestseller row, and product id we get by getProductId()
        $productId = $row->getId();

        // No url is possible for non-existing products
        if (!$productId) {
            return '';
        }

        $params = array('id' => $productId);
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}
