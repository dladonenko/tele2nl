<?php

class Tele4G_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    public $sourceSubscriptionType;
    public $brandName;
    public $brandUrl;

    protected function _prepareLayout()
    {
        $sourceSubscriptionType = $this->getRequest()->getParam('subscriptionType', 'post');
        if ($sourceSubscriptionType == 'post' || $sourceSubscriptionType == 'pre') {
            $this->sourceSubscriptionType = $sourceSubscriptionType;
        } else {
            $this->sourceSubscriptionType = 'post';
        }

        return parent::_prepareLayout();
    }

    public function getExpectedDeliveryTime(Mage_Catalog_Model_Product $product)
    {
        $expectedDays = $expectedWeeks = 0;
        $virtualStockModel = Mage::getModel('tele2_cataloginventory/virtualstock');
        $stockItemModel = Mage::getModel("catalogInventory/stock_item")->load($product->getId(), 'product_id');
        if ($product->getIsInStock() == Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK) {
            $expectedDays = 1;
            if ($stockItemModel->getQty() <=0 && $stockItemModel->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY) {
                $expectedDays = $virtualStockModel->getExpectedDeliveryTime($product);
            }
            if ($expectedDays > 7) {
                $expectedWeeks = $virtualStockModel->daysToWeeks($expectedDays);
            }
        }

        return array(
            "expectedDays" => $expectedDays,
            "expectedWeeks" => $expectedWeeks
        );
    }

    public function getPrePostSubscription()
    {
        $listBlock = $this->getLayout()->createBlock('tele4G_catalog/product_list');
        $subscriptions = $listBlock->getPrePostSubscription($this->getProduct());

        return $subscriptions;
    }

    public function getIsDongle()
    {
        return Mage::helper('tele2_catalog')->isDongle($this->getProduct());
    }

    public function getIsSubscription()
    {
        return Mage::helper('tele2_catalog')->isSubscription($this->getProduct());
    }

    public function getIsFmcg()
    {
        $product = $this->getProduct();
        $fmcgCategory = Mage::getModel('catalog/category')->loadByAttribute('code', 'fmcg');
        if ($fmcgCategory) {
            $fmcgCategoryId = $fmcgCategory->getId();
            $productCategoriesIds = $product->getCategoryIds();

            if (in_array($fmcgCategoryId, $productCategoriesIds)) {
                return true;
            }
        }
        return false;
    }

    public function getExpectedDeliveryTimeShow()
    {
        $expectedDeliveryTimeShow = "";
        if ($this->getIsDongle()) {
            $expectedDeliveryTime = $this->getLayout()->createBlock('tele4G_catalog/product_view')->getExpectedDeliveryTime($this->getProduct());
            if ($expectedDeliveryTime['expectedDays']== 0) {
                $expectedDeliveryTimeShow = $this->__('Expected delivery time is not available');
            } else {
                if ($expectedDeliveryTime['expectedWeeks'] > 0) {
                    $expectedDeliveryTimeShow = $expectedDeliveryTime['expectedWeeks'] . '-' . ($expectedDeliveryTime['expectedWeeks']+1) . ' ' . $this->__('veckor');
                } else {
                    $expectedDeliveryTimeShow = $expectedDeliveryTime['expectedDays'] . '-' . ($expectedDeliveryTime['expectedDays']+2) . ' ' . $this->__('dagar');
                }
            }
        }
        return $expectedDeliveryTimeShow;

    }

    public function getBrandName()
    {
        $product = $this->getProduct();
        $brand_category = Mage::getModel('catalog/category')->loadByAttribute('code', 'brands');
        $_category_ids = $product->getCategoryIds();
        if ($brand_category) {
            foreach ($brand_category->getChildrenCategories() as $brand) {
                if (in_array($brand->getId(), $_category_ids)) {
                    $this->brandName = $brand->getName();
                    $this->brandUrl = $brand->getUrl();
                    return $this->brandName;
                }
            }
        }
        return null;
    }

    /**
     * @return null
     * @todo move to another block; fix getting product
     */
    public function getResellers()
    {
        $city = $this->getRequest()->getParam('city', '');
        $productId = $this->getRequest()->getParam('product', '');

        if (!$city) {
            return null;
        }

        $product = $this->getProduct();
        if (!$product) {
            $this->setProductId($productId);
            $product = $this->getProduct();
        }
        $resellers = null;
        if ($product) {
            $ss4IntegrationHelper = Mage::helper("tele4G_sS4Integration/data");
            $ss4Result = $ss4IntegrationHelper->getResellersForArticleAndCity(array("city" => $city, "article_id" => $product->getPartnerid()));
            if (isset($ss4Result->result->resellers->reseller)) {
                $resellers = array();
                if (is_array($ss4Result->result->resellers->reseller)) {
                    foreach ($ss4Result->result->resellers->reseller as $_reseller) {
                        if (isset($_reseller->stockLevelAmount) && $_reseller->stockLevelAmount > 0) {
                            $resellers[] = $_reseller;
                        }
                    }
                } else {
                    $_reseller = $ss4Result->result->resellers->reseller;
                    if (isset($_reseller->stockLevelAmount) && $_reseller->stockLevelAmount > 0) {
                        $resellers[] = $_reseller;
                    }
                }
            }
            if (is_array($resellers)) {
                return $resellers;
            }
        }

        return null;
    }

    public function getResellerCities()
    {
        $ss4IntegrationHelper = Mage::helper("tele4G_sS4Integration/data");
        $citiesToGo = $ss4IntegrationHelper->getResellerCities();
        if (is_array($citiesToGo)) {
            return $citiesToGo;
        }

        return null;
    }
}