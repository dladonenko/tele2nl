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
class Tele2_Subscription_Block_Adminhtml_Config_Container extends Tele2_Subscription_Block_Adminhtml_Container
{

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/configNew');
    }

}
