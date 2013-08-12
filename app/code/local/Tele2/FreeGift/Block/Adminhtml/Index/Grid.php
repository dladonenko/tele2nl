<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_FreeGift_Block_Adminhtml_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('FreeGiftGrid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * PrepareCollection method.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getResourceModel('tele2_freeGift/freeGift_collection'));
        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Grid URL
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }

    /**
     * Configuration of grid
     *
     * @return Tele2_Subscription_Block_Adminhtml_Index_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => $this->__('Id'),
            'index'     => 'entity_id',
            'type'      => 'text',
            'width'     => '5%',
            'sortable'  => false,
        ));
        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name',
            'type'      => 'text',
            'sortable'  => true,
        ));
        $this->addColumn('coupon_code', array(
            'header'    => $this->__('Coupon Code'),
            'index'     => 'coupon_code',
            'type'      => 'text',
            'sortable'  => true,
        ));
        /*
        $this->addColumn('discount', array(
            'header'    => $this->__('Base discount'),
            'index'     => 'discount',
            'type'      => 'text',
            'align'     => 'center',
            'width'      => '25%',
            'sortable'  => false,
        ));
        */

        return $this;
    }
}
