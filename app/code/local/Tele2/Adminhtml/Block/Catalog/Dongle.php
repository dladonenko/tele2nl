<?php

class Tele2_Adminhtml_Block_Catalog_Dongle extends Mage_Adminhtml_Block_Widget_Container
{

	/**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product.phtml');
    }

    /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
		$attributeSetId = Mage::getModel('eav/entity_attribute_set')
			->load(Tele2_Install_Helper_Data::ATTR_SET_DONGLE, 'attribute_set_name')
			->getAttributeSetId();

		$this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add Dongle'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new/set/' . $attributeSetId . '/type/' . Mage_Catalog_Model_Product_Type::TYPE_SIMPLE . '/')}')",
            'class'   => 'add'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('tele2_adminhtml/catalog_dongle_grid', 'product.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Deprecated since 1.3.2
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
		   return false;
        }
        return true;
    }
}
