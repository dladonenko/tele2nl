<?php 
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_FreeGift_Block_Adminhtml_FreeGift_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_freeGift = null;

    protected function _getFreeGift()
    {
        if (!$this->_freeGift) {
            $this->_freeGift = Mage::registry('freeGift');
        }
        return $this->_freeGift;
    }

    public function getAddons()
    {
        return Mage::helper('tele2_freeGift')
            ->getProductsAsOptions(Tele2_Install_Helper_Data::ATTR_SET_ADDON);
    }

    public function getAccessories()
    {
        return Mage::helper('tele2_freeGift')
            ->getProductsAsOptions(Tele2_Install_Helper_Data::ATTR_SET_ACCESSORY);
    }

    public function getDevices()
    {
        return Mage::helper('tele2_freeGift')
            ->getProductsAsOptions(Tele2_Install_Helper_Data::ATTR_SET_DEVICE);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('tele2_freeGift')->__('Gifts');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('tele2_freeGift')->__('Gifts');
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
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
