<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Tab_Config_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('config_grid');
        $this->setDefaultSort('config_id');
        $this->setUseAjax(true);
        if ($this->_getSubscription()->getSubscriptionId()) {
            $this->setDefaultFilter(array('config' => 1));
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getSubscription()
    {
        return Mage::registry('subscription');
    }


    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tele2_subscription/config')
            ->getCollection()
            //->addAttributeToSelect('*')
            ;

        //$collection->getSelect()->where(" `e`.attribute_set_id = '{$addonAttributeSetId}'");

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
        if ($column->getId() == 'config') {
            $productIds = $this->_getSelectedConfig();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                //$this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    //$this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
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
                'field_name'        => 'selected_config[]',
                'values'            => $this->_getSelectedConfig(),
                'align'             => 'center',
                'index'             => 'config_id',
                'sortable'          => false,
                'filterable'        => false
        ));

        $this->addColumn('config_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'config_id'
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'sortable'  => true,
            'width'     => '70%',
            'index'     => 'name'
        ));

        $this->addColumn('display_in_cart', array(
            'header'    => Mage::helper('catalog')->__('Display in cart'),
            'width'     => '30',
            'index'     => 'display_in_cart',
            'type'      => 'options',
            'options'   => Mage::getModel("adminhtml/system_config_source_yesno")->toArray()
        ));

        $this->addColumn('priceplan', array(
            'header'        => Mage::helper('catalog')->__('Price Plan Code'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'priceplan'
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
        return $this->getUrl('*/*/associatedConfigGrid', array('_current' => true, 'subscription_id' => $this->_getSubscription()->getSubscriptionId()));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedConfig()
    {
        $products = Mage::getModel('tele2_subscription/configRelation')
            ->getSubscriptionConfigs($this->_getSubscription()->getSubscriptionId());

        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $products = array();
        foreach (Mage::registry('current_product')->getRelatedProducts() as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Page Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

}