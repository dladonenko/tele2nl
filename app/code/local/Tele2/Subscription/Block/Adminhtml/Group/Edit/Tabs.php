<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Tabs
 */

class Tele2_Subscription_Block_Adminhtml_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('group_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('tele2_subscription')->__('Group Information'));
    }
}
