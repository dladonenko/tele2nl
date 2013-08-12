<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Tab_Addons extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_subscription = null;

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('addons_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getSubscription()->getSubscriptionId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getSubscription()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = Mage::registry('current_subscription');
        }
        return $this->_subscription;
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
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedAddons();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*');

        $addonAttributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load(Tele2_Install_Helper_Data::ATTR_SET_ADDON, 'attribute_set_name')
            ->getAttributeSetId();

        $collection->getSelect()->where(" `e`.attribute_set_id = '{$addonAttributeSetId}'");

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('addons', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'addons',
                'field_name'        => 'selected_addon[]',
                'values'            => $this->_getSelectedAddons(),
                'align'             => 'center',
                'index'             => 'entity_id',
                'sortable'          => false,
                'filterable'        => false
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('product_price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
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
        return $this->getUrl('*/*/mobileaddonsrelatedgrid', array('_current' => true, 'subscription_id' => $this->_getSubscription()->getSubscriptionId()));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedAddons()
    {
        $products = Mage::getModel('tele2_subscription/addonRelation')
            ->getSubscriptionAddons($this->_getSubscription()->getSubscriptionId());

        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedAddons()
    {
        $addons = array();
        $subscription = Mage::registry('current_subscription');
        foreach (Mage::registry('current_subscription')->getAddons() as $addon) {
            $addons[$addon->getAddonId()] = 0;
        }
        return $addons;
    }
}