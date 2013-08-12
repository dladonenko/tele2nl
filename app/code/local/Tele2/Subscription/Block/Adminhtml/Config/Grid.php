<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Block_Adminhtml_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('SubscriptionConfigGrid');
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
        $this->setCollection(Mage::getResourceModel('tele2_subscription/config_collection'));
        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/configGrid', array('_current' => true));
    }

    /**
     * Grid URL
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/configEdit', array('config_id' => $row->getId()));
    }

    /**
     * Configuration of grid
     *
     * @return Tele2_Subscription_Block_Adminhtml_Index_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('config_id', array(
            'header'    => $this->__('Id'),
            'index'     => 'config_id',
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
        $this->addColumn('price_with_vat', array(
            'header'    => $this->__('Price With VAT'),
            'index'     => 'price_with_vat',
            'type'      => 'text',
            'sortable'  => true,
        ));
        $this->addColumn('price_without_vat', array(
            'header'    => $this->__('Price Without VAT'),
            'index'     => 'price_without_vat',
            'type'      => 'text',
            'sortable'  => true,
        ));

        return $this;
    }
}
