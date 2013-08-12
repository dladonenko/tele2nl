<?php
/**
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Adminhtml_Block_Catalog_Product_Edit_Tab_Offers_Offer extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/tab/offers/offer.phtml');
    }

    public function getProduct()
    {
        $product = Mage::registry('current_product');

        $rewriteModel = Mage::getModel('core/url_rewrite')->loadByIdPath('product/' . $product->getId());
        $product->setProductOfferUrl($rewriteModel->getRequestPath());

        $typeInstance = $product->getTypeInstance(true);
        if ($typeInstance->isComposite()) {
            $productAttributeOptions = $typeInstance->getConfigurableAttributesAsArray($product);
            if (is_array($productAttributeOptions) && count($productAttributeOptions)) {
                $colorOptions = array();
                foreach ($productAttributeOptions as $productAttribute) {
                    if ($productAttribute['attribute_code'] == 'color') {
                        foreach ($productAttribute['values'] as $val) {
                            $colorOptions[] = $val['default_label'];
                        }
                        break;
                    }
                }
                if (count($colorOptions)) {
                    $product->setColorLables($colorOptions);
                }
            }
        }

        return $product;
    }

    /**
     * @return Collection of subscription-product relations with all fields
     * @todo move to model, limit selected fields
     */
    public function getOffers()
    {
        $relationCollection = Mage::getResourceModel('tele2_subscription/relation_collection');
        $relationCollection->joinProducts();
        $select = $relationCollection->getSelect();
        $select->joinInner(array('s' => 'tele2_abstract_subscription'), 's.subscription_id=main_table.subscription_id');
        $select->joinInner(array('sa' => 'tele2_abstract_subscription_attributes'), 'sa.subscription_id=main_table.subscription_id');
        $select->joinInner(array('sm' => 'tele2_mobile_subscription'), 'sm.subscription_id=main_table.subscription_id');
        $select->joinInner(array('sma' => 'tele2_mobile_subscription_attributes'), 'sma.subscription_id=main_table.subscription_id');
        $relationCollection->addFieldToFilter('main_table.product_id', $this->getProduct()->getId());

        return $relationCollection;
    }

    /**
     * @return array|bool Get names and urls for all stores where product is presented
     */
    public function getStores()
    {
        $storeIds = $this->getProduct()->getStoreIds();
        if (count($storeIds)) {
            $storesData = array();
            foreach ($storeIds as $id) {
                $store = Mage::app()->getStore($id);
                $storesData[] = array(
                    'name'=>$store->getName(),
                    'url'=>Mage::getStoreConfig('web/unsecure/base_url', $id),
                    'code'=>$store->getCode(),
                    'id'=>$id,
                );
            }
            return $storesData;
        }
        return false;
    }
}
