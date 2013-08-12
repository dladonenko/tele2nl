<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_FreeGift_Block_Adminhtml_FreeGift_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * FreeGift instance
     * @var null | Tele2_FreeGift_Model_FreeGift
     */
    private $_freeGift = null;
    

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('tele2_freeGift')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('tele2_freeGift')->__('Conditions');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrive current Subscription entity
     * 
     * @return Tele2_Subscription_Model_subscription
     */
    public function getFreeGift()
    {
        if (is_null($this->_freeGift)) {
            $this->_freeGift = Mage::registry('freeGift');
        }
        return $this->_freeGift;
    }

    public function getDevices()
    {
        return Mage::helper('tele2_freeGift')
            ->getProductsAsOptions(Tele2_Install_Helper_Data::ATTR_SET_DEVICE);
    }
}
