<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_FreeGifts
 */

/**
 * General Logging container
 */
class Tele2_FreeGift_Block_Adminhtml_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Curent event data storage
     *
     * @deprecated after 1.6.0.0
     * @var object
     */
    protected $_eventData = null;

    /**
     * Remove add button
     * Set block group and controller
     *
     */
    public function __construct()
    {
        $action = Mage::app()->getRequest()->getActionName();
        $this->_blockGroup = 'tele2_freeGift';
        $this->_controller = 'adminhtml_' . $action;

        parent::__construct();
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('tele2_freeGift')->__($this->getData('header_text'));
    }
}
