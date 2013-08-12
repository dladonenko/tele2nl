<?php

class Tele4G_Catalog_Block_Product_View_Subscription extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if ($product->getAttributeText('subscription_type2') == 'mbb') {
            $this->setTemplate('catalog/product/view_subscription_mbb.phtml');
        } else {
            $this->setTemplate('catalog/product/view_subscription.phtml');
        }

        return parent::_prepareLayout();
    }
}