<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */


class Tele4G_Payment_Block_Form_CashOnDelivery extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
//        $this->setTemplate('tele4G/payment/info.phtml');
    }

    public function getInfo()
    {
        $info = $this->getData('info');
        if ($info) {
            if (!($info instanceof Mage_Payment_Model_Info)) {
                Mage::throwException($this->__('Cannot retrieve the payment info model object.'));
            }
            return $info;
        }
    }
    
    public function getMethod()
    {
        if ($this->getInfo()) {
            return $this->getInfo()->getMethodInstance();
        }
        
        return parent::getMethod();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('payment_form_block_to_html_before', array(
            'block'     => $this
        ));
        return parent::_toHtml();
    }
}