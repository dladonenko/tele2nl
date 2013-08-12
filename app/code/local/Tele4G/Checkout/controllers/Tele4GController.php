<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

require ROOT_PATH.'app/code/core/Mage/Checkout/controllers/OnepageController.php';
class Tele4G_Checkout_Tele4GController extends Mage_Checkout_OnepageController
{
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getStepHtml($step = NULL)
    {
        if(!$step){
            return ;
        }
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_tele4G_'.$step);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }
    
    
    
    protected function _unsuccessStep($result)
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_tele4G_step4unsuccess');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }
    
    
    

    
    protected function _getCart()
    {
        return Mage::getSingleton('tele4G_checkout/cart');
    }
    
    protected function _getLastOrder()
    {
        $lastId =  Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastId);
        return $order;
    }
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    
    /**
     * Log Order Create
     */
    public function checkAction()
    {
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();
        Mage::helper('tele4G_sS4Integration')->createOrderLog($lastOrderId);
    }
    
    /*
    public function indexAction()
    {
        $response = Mage::getModel('tele4G_sS4Integration/sS4Integration')
                    ->creatOrder($order);
        print_r($response); 
    }
    */
    public function stepOneAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $this->getOnepage()->saveCheckoutMethod('guest');
            $data = $this->getRequest()->getPost('billing', array());
            
            if (empty($data)){
                $php_errormsg = $this->__('Enter valid Personnummer');
            } elseif (empty($data['email'])){
                $php_errormsg = $this->__('Enter email');
            } elseif ($data['email'] != $data['email_ver'] ){
                $php_errormsg = $this->__('Email does not match');
            } else {
                $php_errormsg = '';
            }

            if($php_errormsg){
                $this->getResponse()->setBody('<script>alert("'.$php_errormsg.'")</script>');
            } else {
                $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
                $data['use_for_shipping'] = 1;
                $data['country_id'] = "Netherlands";
                
                
                if (isset($data['email'])) {
                    $data['email'] = trim($data['email']);
                }
                
                $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
                $this->_getSession()->setBillingData($data);                
                $this->getResponse()->setBody($this->_getStepHtml('step2'));
                
            }
        }
    }
    
    
    public function stepTwoAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $additional = $this->getRequest()->getPost('payment');
            if (!is_null($additional)) {
                $this->getOnepage()
                ->getQuote()
                ->setAdditional($additional)
                ->save();
            }
            $result = new Varien_Object();
            $this->getResponse()->setBody($this->_getStepHtml('step3'));
        }
        
    }
    
    
    public function stepThreeAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        
        if ($this->getRequest()->getParams()) {
            $data = $this->getRequest()->getParam('shipping_method', '');

            if(empty($data)){
                $php_errormsg = $this->__('Choose delivery type');
                $this->getResponse()->setBody('<script>alert("'.$php_errormsg.'")</script>');
            } else {
                $result = $this->getOnepage()->saveShippingMethod($data);
                /*
                $result will have erro data if shipping method is empty
                */
                if(!$result) {
                    Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                    $this->getOnepage()->getQuote()->collectTotals();
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                
                $this->savePayment();
                    
                $dataForLog['payment_method'] = $this->_getSession()->getPaymentMethod();
                $dataForLog['factura'] = $this->getOnepage()->getQuote()->getAdditional();
                foreach ($this->getOnepage()->getQuote()->getAllItems() as $quoteItem) {
                    $dataForLog['quoteItems'][$quoteItem->getItemId()] = $quoteItem->getProductId();
                }

                $result = array();
                
                try {
                    if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                        $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                        if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                            $result['success'] = false;
                            $result['error'] = true;
                            $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                            $this->getResponse()->setBody(_unsuccessStep($result));
                            return;
                        }
                    }

                    $payment_method = $this->_getSession()->getPaymentMethod();
                    $this->getOnepage()->getQuote()->getPayment()->importData(array('method' => $payment_method));
                    $this->getOnepage()->saveOrder();

                    $result['error'] = false;
                    $result['success']   = true;
                } catch (Mage_Payment_Model_Info_Exception $e) {
                    Mage::log("Payment. SaveOrder exception with quote_id: {$this->getOnepage()->getQuote()->getId()} \n data: " . print_r($dataForLog, true));
                    $message = $e->getMessage();
                    if( !empty($message) ) {
                        $result['error_messages'] = $message;
                    }
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } catch (Mage_Core_Exception $e) {
                    Mage::logException($e);
                    Mage::log("Core. SaveOrder exception with quote_id: {$this->getOnepage()->getQuote()->getId()} \n data: " . print_r($dataForLog, true));
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $e->getMessage();

               

                    if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                        if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                            $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                            $result['update_section'] = array(
                                'name' => $updateSection,
                                'html' => $this->$updateSectionFunction()
                            );
                        }
                        $this->getOnepage()->getCheckout()->setUpdateSection(null);
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::log("SaveOrder exception with quote_id: {$this->getOnepage()->getQuote()->getId()} \n data: " . print_r($dataForLog, true));
                    Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
                    $result['success']  = false;
                    $result['error']    = true;
                    $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
                }

                $this->getOnepage()->getQuote()->save();

                $lastOrder = $this->_getLastOrder();
                $result['payment_error'] = Mage::helper("tele4G_checkout")->getPaymentError($payment_method, $lastOrder);
                
                
                if( $result['success']){
                    $this->getResponse()->setBody($this->_getStepHtml('step4'));
                } else {
                    $this->_getSession()->setOrderErrors(serialize($result));
                    $this->getResponse()->setBody($this->_unsuccessStep());
                }
            }
        }
    }

    public function savePayment()
    {        
        $subtotal = $this->getOnepage()
                        ->getQuote()
                        ->getSubtotal();

        $shipping = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();
        $this->getOnepage()->savePayment(array('method' => 'tele4G_cashondelivery'));
        $this->_getSession()->setPaymentMethod('tele4G_cashondelivery');
    }
    
}
