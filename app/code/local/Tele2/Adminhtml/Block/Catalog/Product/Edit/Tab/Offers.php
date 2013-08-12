<?php
/**
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Adminhtml_Block_Catalog_Product_Edit_Tab_Offers
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function _construct()
    {
        parent::_construct();
 
        $this->setTemplate('catalog/product/edit/tab/offers.phtml');
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('offers_box',
            $this->getLayout()->createBlock('tele2_adminhtml/catalog_product_edit_tab_offers_offer')
        );

        return parent::_prepareLayout();
    }

    public function getOffersBoxHtml()
    {
        return $this->getChildHtml('offers_box');
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return $this->__('Offers');
    }

    public function getTabTitle() 
    {
        return $this->__('Click here to view offers for the product');
    }

    public function isHidden() 
    {
        return false;
    }
}
