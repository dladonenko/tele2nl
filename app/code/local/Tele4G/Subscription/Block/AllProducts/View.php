<?php
/**
 * Tele4G AllProducts View block
 *
 * @category   Tele4G
 * @package    Tele4G_Subscription
 */
class Tele4G_Subscription_Block_AllProducts_View extends Mage_Core_Block_Template
{
    /**
     * Retrive Product list html
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('allProducts.product_list');
    }
}
