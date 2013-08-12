<?php

class Tele2_Adminhtml_Block_Catalog_Subscription_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

	protected function _prepareCollection()
	{
		$store = $this->_getStore();
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('attribute_set_id')
			->addAttributeToSelect('type_id');

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
			->load(Tele2_Install_Helper_Data::ATTR_SET_SUBSCRIPTION, 'attribute_set_name')
			->getAttributeSetId();
		$collection->addFieldToFilter('attribute_set_id', $attributeSetId);

		$this->setCollection($collection);

		//parent::_prepareCollection();
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
		$this->getCollection()->addWebsiteNamesToResult();
		return $this;
	}

	protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->removeColumn('set_name');

        return $this;
    }

}
