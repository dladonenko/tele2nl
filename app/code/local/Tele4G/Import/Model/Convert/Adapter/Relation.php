<?php
class Tele4G_Import_Model_Convert_Adapter_Relation extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    protected function _saveAccessoryCompatity($deviceId, $accessoryIds)
    {
        if ($deviceId && count($accessoryIds)) {
            $device = Mage::getModel('catalog/product')
                ->loadByAttribute('articleid', $deviceId);
            if ($device && $device->getId()) {
                $accessoryModel = Mage::getModel('catalog/product');

                $relatedArray = array();

                $i = 0;
                foreach ($accessoryIds as $key => $id) {
                    $accessory = $accessoryModel->loadByAttribute('articleid', $id);
                    if ($accessory && $accessory->getId()) {
                        $relatedArray[$accessory->getId()] = array('position'=>$i++);
                    }
                }

                if(count($relatedArray)){
                    $device->setRelatedLinkData($relatedArray);
                    $device->save();
                }
            }
        }
    }

    protected function _saveHardwareVariants($variantGroup)
    {
        $productModel = Mage::getModel('catalog/product');

        $master = $productModel->loadByAttribute('articleid', $variantGroup['masterArticleId']);
        if (!$master || !$master->getId()) {
            $message = Mage::helper('catalog')->__('Product with article id %s does not exist', $variantGroup['masterArticleId']);
            Mage::throwException($message);
        }

        $configurableMasterData = $master->getData();

        $master->setVariantMaster(1);
        $master->save();

        //set configurable attributes
        $colorAttribute = 'color';
        $colorAttributeId = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $colorAttribute)->getId();
        $colorValuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($colorAttributeId)
            ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID, false)
            ->load();

        $simpleProducts = array();
        foreach ($variantGroup['associates'] as $key=>$val) {
            $model = Mage::getModel('catalog/product');
            $product = $model->loadByAttribute('articleid', $val);
            if($product){
                array_push(
                    $simpleProducts,
                    array(
                        "id" => $product->getId(),
                        "price" => $product->getPrice(),
                        "attr_code" => $colorAttribute,
                        "attr_id" => $colorAttributeId,
                        "value" => $this->_getAttributeOptionValue($colorAttribute, $product->getAttributeText($colorAttribute)),
                        "label" => $product->getAttributeText($colorAttribute)
                    )
                );
            }
        }

        $productModel->unsetData();
        $productModel->setData($configurableMasterData);
        $productModel->setId(null);
        $productModel->setSku($variantGroup['groupId']);
        $productModel->setArticleid($variantGroup['groupId']);
        $productModel->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
        $productModel->setStatus(1);
        $productModel->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);

        $category = Mage::getModel('catalog/category')->loadByAttribute('name', 'Configurable Devices');
        if ($category && $category->getId()){
            $productModel->setCategoryIds(array($category->getId()));
        }

        $configProductTypeInstance = $productModel->getTypeInstance();
        $configProductTypeInstance->setUsedProductAttributeIds(array($colorAttributeId));
        $attributes_array = $configProductTypeInstance->getConfigurableAttributesAsArray();
        foreach($attributes_array as $key => $attribute_array) {//???
            $attributes_array[$key]['use_default'] = 1;
            $attributes_array[$key]['position'] = 0;

            if (isset($attribute_array['frontend_label'])) {
                $attributes_array[$key]['label'] = $attribute_array['frontend_label'];
            } else {
                $attributes_array[$key]['label'] = $attribute_array['attribute_code'];
            }
        }

        $dataArray = array();
        foreach ($simpleProducts as $simpleArray) {
            $dataArray[$simpleArray['id']] = array();
            foreach ($attributes_array as $attrArray) {
                array_push(
                    $dataArray[$simpleArray['id']],
                    array(
                        "attribute_id" => $simpleArray['attr_id'],
                        "label" => $simpleArray['label'],
                        "is_percent" => false,
                        "pricing_value" => $simpleArray['price']
                    )
                );
            }
        }

        $productModel->setCanSaveConfigurableAttributes(true);
        $productModel->setCanSaveCustomOptions(true);
        $productModel->setConfigurableProductsData($dataArray);

        $productModel->save();
    }

    protected function _getAttributeOptionValue($arg_attribute, $arg_value) {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option) {
            if ($option['label'] == $arg_value) {
                return $option['value'];
            }
        }

        return false;
    }

    protected function _addAttributeOption($arg_attribute, $arg_value) {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        $value['option'] = array($arg_value,$arg_value);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        $attribute->save();

        return $this->_getAttributeOptionValue($arg_attribute, $arg_value);
    }

    public function saveRow($importedItemObject)
    {
        $importedItemObject = unserialize($importedItemObject);

        //Mage::log(print_r($importedItemObject, true), null, 'RowsInSaveRow.log');

        $this->_saveHardwareVariants($importedItemObject);
        if (isset($importedItemObject['accessories'])) {
            $this->_saveAccessoryCompatity($importedItemObject['groupId'], $importedItemObject['accessories']);
        }

        return true;
    }

    public function load()
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }
}
