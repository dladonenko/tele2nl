<?php
class Tele4G_Import_Model_Convert_Adapter_Product extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    protected $_productTypeMap = array(
            'MOBILE_PHONE'=>array('attributeset'=>'device', 'category'=>'Simple Devices'),
            'PLUS_SERVICE'=>array('attributeset'=>'addon', 'category'=>'Add-ons'),
            'ACCESSORY'=>array('attributeset'=>'accessory', 'category'=>'Accessories'),
            'LAPTOP'=>array('attributeset'=>'device', 'category'=>'Simple Devices'),//laptop attributeset and category should be created
            'USB_MODEM'=>array('attributeset'=>'dongle', 'category'=>'Mobile broadband'),
            'MOBILE_PREPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'),  //deprecated
            'MOBILE_VOICE_PREPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'), //renamed from MOBILE_PREPAID
            'MOBILE_POSTPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'), //deprecated
            'MOBILE_VOICE_POSTPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'), //renamed from MOBILE_POSTPAID
            'MOBILE_BROADBAND_PREPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'),
            'MOBILE_BROADBAND_POSTPAID'=>array('attributeset'=>'subscription', 'category'=>'Subscriptions'),
    );

    protected $_subscriptionTypesMap = array(
        'MOBILE_PREPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE, //deprecated
        'MOBILE_VOICE_PREPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_PRE, //renamed from MOBILE_PREPAID
        'MOBILE_POSTPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST, //deprecated
        'MOBILE_VOICE_POSTPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_POST, //renamed from MOBILE_POSTPAID
        'MOBILE_BROADBAND_PREPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_PRE,
        'MOBILE_BROADBAND_POSTPAID'=>Tele2_Subscription_Model_Mobile::SUBSCRIPTION_TYPE1_BB_POST,
    );

    protected $_colorMap = array(
        'PURPLE'=>'Purple',
        'UNKNOWN'=>'Unknown',
        'BLUE'=>'Blue',
        'WHITE'=>'White',
        'BLACK'=>'Black',
        'YELLOW'=>'Yellow',
        'PINK'=>'Pink',
        'GREY'=>'Grey',
        'ORANGE'=>'Orange',
        'GREEN'=>'Green',
        'RED'=>'Red',
        'MAGENTA'=>'Magenta',
        'SILVER'=>'Silver',
        'BROWN'=>'Brown',
        'GOLD'=>'Gold',
    );
    /**
     * @todo: clean it all
     * @param $compatibility
     */
    protected function _saveAddonCompatity($compatibility)
    {
        if (!isset($compatibility['plusServiceId'])) {
            $message = Mage::helper('catalog')->__('Skip import row, required field plusServiceId or subscriptionId not defined');
            Mage::throwException($message);
        }

        $productModel = Mage::getModel('catalog/product');
        $subscriptionModel = Mage::getModel('tele2_subscription/mobile');

        $addon = $productModel->loadByAttribute('articleid', $compatibility['plusServiceId']);

        if (isset($compatibility['subscriptionId'])) {
            $subscription = $subscriptionModel->load($compatibility['subscriptionId'], 'articleid');
        }

        if ($addon) {
            $addonId = $addon->getId();
        }

        if (isset($addonId)) {
            $subscriptionType = $compatibility['subscriptionType'];
            $stypeId = $this->_subscriptionTypesMap[$subscriptionType];

            $relationModel = Mage::getModel('tele2_subscription/addonRelation');
            $relationModel->setAddonId($addonId);

            if (isset($subscription) && $subscription->getId()) {
                $relationModel->setSubscriptionId($subscription->getId());
            }

            $relationModel->setStypeId($stypeId);
            $relationModel->save();
        }
    }

    protected function _prepareProductData($importedDataObject, $attributeSetName = 'Default')
    {
        $data = array(
            'attribute_set_id' => Mage::getModel('eav/entity_attribute_set')->load($attributeSetName, 'attribute_set_name')->getAttributeSetId(),
            'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            'sku' => $importedDataObject['id'],
            'articleid' => $importedDataObject['id'],
            'name' => $importedDataObject['name'],
            'url_key' => implode('-', explode(' ', mb_strtolower($importedDataObject['name']))),
            'url_path' => implode('-', explode(' ', mb_strtolower($importedDataObject['name']))),
            'weight' => sprintf("%0.2f", 100),
            'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,//STATUS_ENABLED//STATUS_DISABLED
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,//VISIBILITY_BOTH
            'tax_class_id' => '0',
            'variant_master' => '0',
            'description' => isset($importedDataObject['description']) ? $importedDataObject['description'] : $importedDataObject['name'],
            'short_description' => isset($importedDataObject['shortDescription']) ? $importedDataObject['shortDescription'] : '',
        );

        return $data;
    }

    /**
     * Saves imported images
     * @param $product
     * @param $imageArray
     */
    protected function _saveProductImages($product, $imageArray)
    {
        $paths = array();
        $fileSuffix = '_270x350.png';
        $imageDir = MAGENTO_ROOT . DS . 'var' . DS . 'import' . DS . 'images' . DS;
        if (count($imageArray)>1) {
            foreach ($imageArray as $url) {
                $imageName = str_ireplace('.png', $fileSuffix, $url['url']);
                $imagePath = $imageDir . $imageName;
                if (file_exists($imagePath)) {
                    $paths[] = $imagePath;
                } else {
                    if ($this->_getImageByCurl($imageName)) {
                        $paths[] = $imageDir . $imageName;
                    }
                }
            }
        } elseif (count($imageArray) == 1) {
            $imageName = str_ireplace('.png', $fileSuffix, $imageArray['url']);
            $imagePath = $imageDir . $imageName;
            if (file_exists($imagePath)) {
                $paths[] = $imagePath;
            } else {
                if ($this->_getImageByCurl($imageName)) {
                    $paths[] = $imageDir . $imageName;
                }
            }
        }

        $count = count($paths);
        for ($i = 0; $i<$count; $i++) {
            if ($i == 0) {
                $mode = array("thumbnail", "small_image", "image");
            } else {
                $mode = array();
            }

            $product->addImageToMediaGallery($paths[$i], $mode, false, false);
        }
    }

    /**
     * Downloads an image from tele2
     * @param $imageName
     * @return bool
     */
    protected function _getImageByCurl($imageName)
    {
        try{
            $sourceUrl = "http://www.tele2.se/foretag/produktbilder/";
            $curlHandler = curl_init ($sourceUrl . $imageName);
            $image = MAGENTO_ROOT . DS . 'var' . DS . 'import' . DS . 'images' . DS . $imageName;

            curl_setopt($curlHandler, CURLOPT_HEADER, 0);
            curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandler, CURLOPT_BINARYTRANSFER,1);
            $rawdata = curl_exec($curlHandler);
            curl_close ($curlHandler);


            if(file_exists($image)){
                unlink($image);
            }
            $fp = fopen($image, 'x');
            fwrite($fp, $rawdata);
            fclose($fp);
            return true;
        }catch(Exception $e){
            $this->addException(Mage::helper('dataflow')->__('Image can not be imported.' . $e->getMessage()));
        }
    }

    protected function _updateProduct($product, $importedItemObject)
    {
        $preparedData = $product->getData();
        foreach ($importedItemObject as $fieldName => $fieldValue) {
            if (strpos($fieldName, 'ss4_') === 0) {
                unset($importedItemObject[$fieldName]);
            }
        }
        $preparedData = array_merge($preparedData, $importedItemObject);
        $preparedData['status'] = Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
        $preparedData['visibility'] = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;

        $product->setData($preparedData);

        if (is_array($importedItemObject['images']['image'])) {
            $this->_saveProductImages($product, $importedItemObject['images']['image']);
        }

        if (isset($importedItemObject['upfrontPrice']['priceWithVat'])) {
            $product->setPrice($importedItemObject['upfrontPrice']['priceWithVat']);
        } else {
            $product->setPrice(0);
        }

        if (isset($importedItemObject['uniqueSellingPoints'])) {
            $product->setUsp($importedItemObject['uniqueSellingPoints']);
        }

        if (isset($importedItemObject['color'])) {
            $colorOptionId = $this->_getAttributeOptionValueId('color', $this->_colorMap[$importedItemObject['color']]);
            if ($colorOptionId) {
                $product->setColor($colorOptionId);
            }
        }

        //update stock (request to ss4)
        Mage::getModel('tele4G_sS4Integration/observer')->updateProduct($product);

        $product->save();
    }

    protected function _getAttributeOptionValueId($attributeCode, $optionTextValue)
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID, false)
            ->load();

        foreach ($valuesCollection as $item) {
            if ($item->getValue() == $optionTextValue) {
                return $item->getId();
            }
        }
        return false;
    }

    protected function _createProduct($importedItemObject, $attributeSetName = 'Default', $categoryName = 'Default Category')
    {
        $productModel = Mage::getModel('catalog/product');
        $preparedData = $this->_prepareProductData($importedItemObject, $attributeSetName);

        $productModel->setData(array_merge($preparedData, $importedItemObject));
        $productModel->setWebsiteIds(array(1));

        $category = Mage::getModel('catalog/category')->loadByAttribute('name', $categoryName);
        if ($category && $category->getId()){
            $productModel->setCategoryIds(array($category->getId()));
        }

        if (is_array($importedItemObject['images']['image'])) {
            $this->_saveProductImages($productModel, $importedItemObject['images']['image']);
        }

        if (isset($importedItemObject['upfrontPrice']['priceWithVat'])) {
            $productModel->setPrice($importedItemObject['upfrontPrice']['priceWithVat']);
        } else {
            $productModel->setPrice(0);
        }

        if (isset($importedItemObject['uniqueSellingPoints'])) {
            $productModel->setUsp($importedItemObject['uniqueSellingPoints']);
            $productModel->setSs4Usp($importedItemObject['uniqueSellingPoints']);
        }

        if (isset($importedItemObject['partnerId'])) {
            $productModel->setPartnerid($importedItemObject['partnerId']);
            $productModel->setSs4Partnerid($importedItemObject['partnerId']);
        }

        if (isset($importedItemObject['color'])) {
            $colorOptionId = $this->_getAttributeOptionValueId('color', $this->_colorMap[$importedItemObject['color']]);
            if ($colorOptionId) {
                $productModel->setColor($colorOptionId);
            }
        }

        if (isset($importedItemObject['supportedSimCardType'])) {
            $simTypeOptionId = $this->_getAttributeOptionValueId('sim_type', $importedItemObject['supportedSimCardType']);
            if ($simTypeOptionId) {
                $productModel->setSimType($simTypeOptionId);
            }
        }

        $stockData = array();
        $stockData['qty'] = 100;
        $stockData['is_in_stock'] = 1;
        $stockData['manage_stock'] = 1;
        $stockData['use_config_manage_stock'] = 0;
        $productModel->setStockData($stockData);

        $productModel->setIsMassupdate(true);
        $productModel->setExcludeUrlRewrite(true);
        $productModel->save();

        return $productModel;
    }

    protected function _createSubscription($importedItemObject)
    {
        $subscriptionModel = Mage::getModel('tele2_subscription/mobile');
        $subscriptionModel->setData($importedItemObject);
        $subscriptionModel->setArticleid($importedItemObject['id']);
        if (isset($importedItemObject['monthlyfee'])) {
            $subscriptionModel->setPrice($importedItemObject['monthlyfee']);
        }
        if (isset($importedItemObject['description'])) {
            $subscriptionModel->setDescription($importedItemObject['description']);
        }
        if (isset($importedItemObject['uniqueSellingPoints'])) {
            $subscriptionModel->setUsp($importedItemObject['uniqueSellingPoints']);
        }
        if (isset($importedItemObject['pricePlan'])) {
            $subscriptionModel->setPriceplan($importedItemObject['pricePlan']);
        }
        if (isset($importedItemObject['subscriptionType']) && isset($this->_subscriptionTypesMap[$importedItemObject['subscriptionType']])) {
            $subscriptionModel->setType1($this->_subscriptionTypesMap[$importedItemObject['subscriptionType']]);
        }

        $subscriptionModel->save();

        if (isset($importedItemObject['monthlyFees']['monthlyFee'])  && is_array($importedItemObject['monthlyFees']['monthlyFee'])) {
            $bindingModel = Mage::getModel('tele2_subscription/binding');
            $subscriptionId = $subscriptionModel->getId();

            foreach($importedItemObject['monthlyFees']['monthlyFee'] as $monthlyFee) {
                $bindingTime = (isset($monthlyFee['bindingTime']) ? $monthlyFee['bindingTime'] : 0);
                $priceWithVat = (isset($monthlyFee['monthlyFee']['priceWithVat']) ? $monthlyFee['monthlyFee']['priceWithVat'] : 0);
                $priceWithoutVat = (isset($monthlyFee['monthlyFee']['priceWithoutVat']) ? $monthlyFee['monthlyFee']['priceWithoutVat'] : 0);

                $bindingModel->setSubscriptionId($subscriptionId);
                $bindingModel->setTime($bindingTime);
                $bindingModel->setMonthlyPriceWithoutVat($priceWithoutVat);
                $bindingModel->setMonthlyPriceWithVat($priceWithVat);

                $bindingModel->save();
                $bindingModel->unsetData();
            }
        }

        return $subscriptionModel;
    }

    protected function _updateSubscription($subscriptionModel, $importedItemObject)
    {
        $subscriptionModel->setName($importedItemObject['name']);

        if (isset($importedItemObject['monthlyfee'])) {
            $subscriptionModel->setPrice($importedItemObject['monthlyfee']);
        }
        if (isset($importedItemObject['description'])) {
            $subscriptionModel->setDescription($importedItemObject['description']);
        }
        if (isset($importedItemObject['uniqueSellingPoints'])) {
            $subscriptionModel->setUsp($importedItemObject['uniqueSellingPoints']);
        }
        if (isset($importedItemObject['pricePlan'])) {
            $subscriptionModel->setPriceplan($importedItemObject['pricePlan']);
        }
        if (isset($importedItemObject['subscriptionType']) && isset($this->_subscriptionTypesMap[$importedItemObject['subscriptionType']])) {
            $subscriptionModel->setType1($this->_subscriptionTypesMap[$importedItemObject['subscriptionType']]);
        }

        if ($subscriptionModel->getResource()->hasDataChanged($subscriptionModel)) {
            $subscriptionModel->save();
        }

        if (isset($importedItemObject['monthlyFees']['monthlyFee'])  && is_array($importedItemObject['monthlyFees']['monthlyFee'])) {
            $bindings = $subscriptionModel->getBindings();

            $newBindings = array();
            foreach($importedItemObject['monthlyFees']['monthlyFee'] as $monthlyFee) {
                $bindingTime = (isset($monthlyFee['bindingTime']) ? $monthlyFee['bindingTime'] : 0);
                $priceWithVat = (isset($monthlyFee['monthlyFee']['priceWithVat']) ? $monthlyFee['monthlyFee']['priceWithVat'] : 0);
                $priceWithoutVat = (isset($monthlyFee['monthlyFee']['priceWithoutVat']) ? $monthlyFee['monthlyFee']['priceWithoutVat'] : 0);

                $newBindings[$bindingTime] = array('monthly_price_with_vat'=>$priceWithVat, 'monthly_price_without_vat'=>$priceWithoutVat);
            }

            if (count($newBindings)) {
                foreach ($bindings as $binding) {
                    if (isset($newBindings[$binding->getTime()])) {
                        $binding->setMonthlyPriceWithoutVat($newBindings[$binding->getTime()]['monthly_price_without_vat']);
                        $binding->setMonthlyPriceWithVat($newBindings[$binding->getTime()]['monthly_price_with_vat']);

                        if ($binding->getResource()->hasDataChanged($binding)) {
                            $binding->save();
                            $binding->unsetData();
                        }
                    }
                }
            }
        }

        return $subscriptionModel;
    }

    public function saveRow($importedItemObject)
    {
        $importedItemObject = unserialize($importedItemObject['serialized']);
        $productType = (isset($importedItemObject['type']) ? $importedItemObject['type'] : null);

        switch ($productType) {
            case 'ADDON_COMPATIBILITY':
                $this->_saveAddonCompatity($importedItemObject);
                break;
            case 'MOBILE_PREPAID':
            case 'MOBILE_POSTPAID':
            case 'MOBILE_VOICE_PREPAID':
            case 'MOBILE_VOICE_POSTPAID':
            case 'MOBILE_BROADBAND_PREPAID':
            case 'MOBILE_BROADBAND_POSTPAID':
                $subscription = Mage::getModel('tele2_subscription/mobile')->getCollection()->addFilter('articleid', $importedItemObject['id'])->getFirstItem();
                if ($subscription && $subscription->getId()) {
                    $this->_updateSubscription($subscription, $importedItemObject);
                } else {
                    $this->_createSubscription($importedItemObject);
                }
                break;
            default: //product (subscription, device, accessory, addon)
                $product = Mage::getModel('catalog/product')->loadByAttribute('articleid', $importedItemObject['id']);
                if ($product && $product->getId()) {
                    $this->_updateProduct($product, $importedItemObject);
                } else {
                    $this->_createProduct($importedItemObject, $this->_productTypeMap[$productType]['attributeset'], $this->_productTypeMap[$productType]['category']);
                }

                break;
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
