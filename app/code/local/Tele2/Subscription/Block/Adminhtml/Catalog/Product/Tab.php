<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Block_Adminhtml_Catalog_Product_Tab
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
 
        $this->setTemplate('subscription/catalog/product/tab.phtml');
    }
	
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Subscription'),
                    'class' => 'add',
                    'id'    => 'add_new_defined_option'
                ))
        );

        $this->setChild('subscription_box',
            $this->getLayout()->createBlock('tele2_subscription/adminhtml_catalog_product_options')
        );

        return parent::_prepareLayout();
    }
	
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
	
    public function getSubscriptionBoxHtml()
    {
        return $this->getChildHtml('subscription_box');
    }

	public function canShowTab()
	{
		return true;
	}

	public function getTabLabel() 
	{
		return $this->__('Subscriptions');
	}

	public function getTabTitle() 
	{
		return $this->__('Click here to view your subscriptions');
	}

	public function isHidden() 
	{
		return false;
	}
}
