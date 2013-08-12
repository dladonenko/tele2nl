<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Block_Adminhtml_Edit_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    public function __construct() 
    {
        parent::__construct();

        $this->setId('adform_track');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() 
    {                
        $collection = Mage::getModel('adform_track/feed')
                            ->getCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();
        
        return $this;
    }

    protected function _prepareColumns() 
    {
        $this->addColumn('feed_id', array(
            'header' => Mage::helper('adform_track')->__('ID'),
            'sortable' => true,
            'index' => 'feed_id',
            'width' => 40,
        ));

        $this->addColumn('created', array(
            'header' => Mage::helper('adform_track')->__('Created'),
            'sortable' => true,
            'index' => 'created',
            'width' => 150,
        ));
        
        $this->addColumn('store_id', array(
            'header' => Mage::helper('adform_track')->__('Store ID'),
            'sortable' => true,
            'index' => 'store_id',
            'width' => 60,
        ));        
        
        $this->addColumn('image_width', array(
            'header' => Mage::helper('adform_track')->__('Image width'),
            'sortable' => true,
            'index' => 'image_width',
            'width' => 50,
        ));
        
        $this->addColumn('image_height', array(
            'header' => Mage::helper('adform_track')->__('Image height'),
            'sortable' => true,
            'index' => 'image_height',
            'width' => 50,
        ));
        
        $this->addColumn('ppf', array(
            'header' => Mage::helper('adform_track')->__('Max per feed'),
            'sortable' => true,
            'index' => 'ppf',
            'width' => 100,
        ));          
        
        $this->addColumn('url_key', array(
            'header' => Mage::helper('adform_track')->__('Url key'),
            'sortable' => true,
            'index' => 'url_key',
            'renderer'  => 'adform_track/adminhtml_edit_renderer_url_key'
        ));
        
        $this->addColumn('selection_type', array(            
            'header' => Mage::helper('adform_track')->__('Selection type'),
            'index' => 'selection_type',
            'type'  => 'options',
            'options' => Mage::getSingleton('adform_track/feed_selection_type')->toOptionArray(),            
            'width' => 80,
        ));         

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Remove'),
                        'url'     => array(
                            'base'=>'*/*/delete',
                            //'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));        
        
        return parent::_prepareColumns();
    }

    public function getGridUrl() 
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
