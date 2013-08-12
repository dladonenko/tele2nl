<?php

class Tele2_Adminhtml_Block_Catalog_Device_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

   
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('color')
            ->addAttributeToSelect('variant_master');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

		$attributeSetId = Mage::getModel('eav/entity_attribute_set')
			->load(Tele2_Install_Helper_Data::ATTR_SET_DEVICE, 'attribute_set_name')
			->getAttributeSetId();

		$collection->addFieldToFilter('attribute_set_id', $attributeSetId);
        $collection->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);

        $this->setCollection($collection);

        //parent::_prepareCollection();
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->removeColumn('type');
        $this->removeColumn('set_name');
        
        $colorVariantModel = Mage::getModel('eav/config')->getAttribute('catalog_product', 'color');
        $colorOptions = $colorVariantModel->getSource()->getAllOptions(true, true);
        $colorOptionsResult = array();
        foreach ($colorOptions as $colorOption) {
            $colorOptionsResult[$colorOption['value']] = $colorOption['label'];
        }
        $this->addColumn('color',
            array(
                'header'=> Mage::helper('catalog')->__('Color'),
                'width' => '60px',
                'index' => 'color',
                'type'  => 'options',
                'options' => $colorOptionsResult,
            )
        );
        $masterVariantModel = Mage::getModel('eav/config')->getAttribute('catalog_product', 'variant_master');
        $masterVariants = $masterVariantModel->getSource()->getAllOptions(true, true);
        $masterVariantResult = array();
        foreach ($masterVariants as $masterVariant) {
            $masterVariantResult[$masterVariant['value']] = $masterVariant['label'];
        }
        $this->addColumn('master',
            array(
                'header'=> Mage::helper('catalog')->__('Master'),
                'width' => '60px',
                'index' => 'variant_master',
                'type'  => 'options',
                'options' => $masterVariantResult,
            )
        );

        $this->addColumn('NameConfigurableProduct',
            array(
                'header'=> Mage::helper('catalog')->__('Name Configurable Product'),
                'width' => '60px',
                'index' => 'entity_id',
                'renderer' => 'Tele2_Adminhtml_Block_Catalog_Product_Grid_Renderer_NameConfigurableProduct',
            )
        );
        
        return $this;
    }

}
