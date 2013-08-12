<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Tabs
 */

class Tele2_Subscription_Block_Adminhtml_Mobile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('subscription_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('tele2_subscription')->__('Mobile Subscription Information'));
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
            $this->addTabAfter('related', array(
                'label'     => Mage::helper('catalog')->__('Associated Products'),
                'url'       => $this->getUrl('*/*/mobileRelated', array('_current' => true)),
                'class'     => 'ajax',
            ), 'binding_section');

            $this->addTabAfter('addons', array(
                'label'     => Mage::helper('catalog')->__('Associated Addons'),
                'url'       => $this->getUrl('*/*/mobileAddonsrelated', array('_current' => true)),
                'class'     => 'ajax',
            ), 'standalone_section');
        
            $this->addTabAfter('config_section', array(
                'label'     => Mage::helper('catalog')->__('Associated Subscription Config'),
                'url'       => $this->getUrl('*/*/mobileConfigrelated', array('_current' => true)),
                'class'     => 'ajax',
            ), 'standalone_section');
        
    }
}
