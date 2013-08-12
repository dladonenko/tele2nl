<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

/**
 * General Logging container
 */
class Tele2_Subscription_Block_Adminhtml_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_blockGroup = 'tele2_subscription';
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
        return Mage::helper('tele2_subscription')->__($this->getData('header_text'));
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/mobileEdit');
    }

}
