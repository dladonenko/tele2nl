<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele4G
 * @package     Tele4G_Payment
 */


class Tele4G_Payment_Block_Form_DownPayment extends Mage_Payment_Block_Form
{
    protected $_payment = null;
    protected $_partPaymentOptions = null;
    protected $_quote = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tele4G/payment/form/downPayment.phtml');
    }

    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payment_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }

    /**
     * Retrive Checkout Quote
     * 
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = Mage::getModel('checkout/session')->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Retrive Payment Options from SS4 WebService
     * 
     * @return stdClass
     */
    public function getOptions()
    {
        if (!$this->_partPaymentOptions) {
            $request = Mage::helper('tele4G_sS4Integration')
                ->getSs4Result(
                    'getPartPayOptions',
                    array('sum' => $this->_getQuote()->getGrandTotal()),
                    'downpayment.options'
                );
            /*
             * This method $request->getResult() does not work, I just removed him, may be Bandrey knows, how it should work correctly
             */
            //$result = $request->getResult();
            $result = $request;
            if (
                isset($result->partPaymentOptions->responseStatus) &&
                $result->partPaymentOptions->responseStatus->status == 'OK' &&
                !$result->partPaymentOptions->responseStatus->errorCode
            ) {
                $this->_partPaymentOptions = $result->partPaymentOptions->partPaymentOption;
            }
        }
        return $this->_partPaymentOptions;
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
