<?php

class Tele4G_Catalog_Model_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
{
    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param stdClass $attributes
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds(),
        );

        $allAttributes = array();
        if (!empty($attributes->attributes)) {
            $allAttributes = array_merge($allAttributes, $attributes->attributes);
        } else {
            foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
                if ($this->_isAllowedAttribute($attribute, $attributes)) {
                    $allAttributes[] = $attribute->getAttributeCode();
                }
            }
        }
        $_additionalAttributeCodes = array();
        if (!empty($attributes->additional_attributes)) {
            foreach ($attributes->additional_attributes as $k => $_attributeCode) {
                $allAttributes[] = $_attributeCode;
                $_additionalAttributeCodes[] = $_attributeCode;
            }
        }
        $_additionalAttribute = 0;
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $product
                        ->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
                }
            }
        }

        $subscriptions = Mage::helper('tele2_catalog/data')->getProductSubscriptions($product);
        if ($subscriptions) {
            $result['subscriptions'] = $subscriptions;
        }

        $associated = $this->_getAssociated($product);
        if ($associated) {
            $result['associated'] = $associated;
        }

        return $result;
    }

    protected function _getAssociated($product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $childrenIds = $product->getTypeInstance(true)->getUsedProductIds($product);
            if (count($childrenIds)) {
                return $childrenIds;
            }
        }

        return array();
    }

}
