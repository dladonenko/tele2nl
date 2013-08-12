<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Lanot_FileManager_Block_Adminhtml_Catalog_Product_Online
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('online_grid');
        $this->setDefaultSort('file_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('online' => 1));
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('lanot_filemanager/FileStorage')
            ->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'online') {
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('config', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'config',
                'field_name'        => 'online_documents[]',
                'values'            => $this->_getSelectedDocumentsIds(),
                'align'             => 'center',
                'index'             => 'file_id',
                'sortable'          => false,
                'filterable'        => false
        ));

        $this->addColumn('file_id', array(
            'header'    => Mage::helper('lanot_filemanager')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'file_id'
        ));
        
        $this->addColumn('filename', array(
            'header'    => Mage::helper('lanot_filemanager')->__('Filename'),
            'sortable'  => true,
            'width'     => '70%',
            'index'     => 'filename'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('lanot_filemanager')->__('Type'),
            'sortable'  => true,
            'width'     => '30',
            'index'     => 'type',
        ));

        $this->addColumn('created_time', array(
            'header'        => Mage::helper('catalog')->__('Created Time'),
            'sortable'  => true,
            'index'         => 'created_time'
        ));

        $this->addColumn('last_modify_time', array(
            'header'        => Mage::helper('catalog')->__('Last Modify Time'),
            'sortable'  => true,
            'index'         => 'last_modify_time'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/onlinedocumentsgrid',
            array('_current' => true, 'product_id' => $this->_getProduct()->getId())
        );
    }

    /**
     * Get Selected documents collection
     *
     * @return mixed
     */
    protected function _getSelectedDocuments()
    {
        return Mage::getModel('lanot_filemanager/FileStorage')
            ->getCollection()->getFilesByProductId($this->_getProduct()->getId());
    }

    /**
     * Get Selected documents ids
     *
     * @return array
     */
    protected function _getSelectedDocumentsIds()
    {
        $documentEntities = array();
        foreach ($this->_getSelectedDocuments() as $documentEntity) {
            $documentEntities[] = $documentEntity->getFileId();
        }
        return $documentEntities;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedOnlineDocuments()
    {
        $documentEntities = array();
        foreach ($this->_getSelectedDocuments() as $documentEntity) {
            $documentEntities[$documentEntity->getFileId()] = 0;
        }
        return $documentEntities;
    }

}