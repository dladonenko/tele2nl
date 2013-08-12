<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Model_Observer
{
    const ONEPAGE_CHECKOUT_STEP_LOGIN = 'checkout.onepage.login';
    const ONEPAGE_CHECKOUT_STEP_BILLING = 'checkout.onepage.billing';
    const ONEPAGE_CHECKOUT_STEP_SHIPPING = 'checkout.onepage.shipping';
    const ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD = 'checkout.onepage.shipping_method';
    const ONEPAGE_CHECKOUT_STEP_PAYMENT = 'checkout.onepage.payment';
    const ONEPAGE_CHECKOUT_STEP_REVIEW = 'checkout.onepage.review';
    
    private $_helper = null;
    
    public function __construct() 
    {
        $this->_helper = Mage::helper('adform_track');
    }
    
    public function injectExternalJs($observer)
    {   
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        if ($observer->getEvent()->getBlock() instanceof Mage_Page_Block_Html_Head) {
            
            $jsLib = Mage::app()->getLayout()
                            ->createBlock('Mage_Core_Block_Text', 'adform_track.external.js.lib');
            
            $jsLib->setText(
                sprintf('<script type="text/javascript" src="%s"></script>', Mage::helper('adform_track')->getJsLibUrl())
            );
            
            $observer->getEvent()->getBlock()->insert($jsLib);
            
            $dividerChar = $this->_helper->getDividerChar();
            
            $title = $observer->getEvent()->getBlock()->getTitle();
            $title = $this->_helper->getPreparedPageTitle($title);
            
            $campaignId = $this->_helper->getCampaignId();
            
            Mage::register('adform.adf.Params.PageName', $title, true);
            Mage::register('adform.adf.Params.Divider', $dividerChar, true);
            Mage::register('adform.campaignId', $campaignId, true);
        }
        
        return $this;
    }
       
    public function injectTrackingLogicJs($observer)
    {           
        //echo Mage::app()->getRequest()->getActionName(); exit;
        
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        if ($observer->getEvent()->getBlock()->getNameInLayout() === 'before_body_end') {
            $js = Mage::app()->getLayout()
                            ->createBlock('Mage_Core_Block_Text', 'adform_track.js.logic');
            
            /* PRODUCT VIEW PAGE */
            /* Mage::app()->getRequest()->getModuleName() => 'catalog' */
            /* Mage::app()->getRequest()->getControllerName() => 'product' */
            /* Mage::app()->getRequest()->getActionName() => 'view' */

            /* CHECKOUT CART PAGE, like http://magento1620.ce/checkout/cart/ */
            /* Mage::app()->getRequest()->getModuleName() => 'checkout' */
            /* Mage::app()->getRequest()->getControllerName() => 'cart' */
            /* Mage::app()->getRequest()->getActionName() => 'index' */
            
            /* CHECKOUT SUCCESS PAGE, like http://magento1620.ce/checkout/onepage/ */
            /* Mage::app()->getRequest()->getModuleName() => 'checkout' */
            /* Mage::app()->getRequest()->getControllerName() => 'onepage' */
            /* Mage::app()->getRequest()->getActionName() => 'success' */
                    
            $title = Mage::registry('adform.adf.Params.PageName');
            $dividerChar = Mage::registry('adform.adf.Params.Divider');
            $campaignId = Mage::registry('adform.campaignId');
            $trackPointImgUrl = $this->_helper->getTrackPointImgUrl();
            
            $request = Mage::app()->getRequest();
            
            if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'onepage' && $request->getActionName() == 'index') {
                //THESE ARE HANDLED TROUGH OTHER FUNCTIONS/OBSERVERS, etc...
            } elseif ($request->getActionName() == 'unsuccess') {
                return;
            } else {
                $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ::'. $request->getActionName() .':: -->'));

                $js->setText($js->getText().'<script type="text/javascript">');

                $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent(%s);adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getUtf8CleanJsonStringFromHtmlString($title), $dividerChar));                
            }            
            
            /* START Handle the PRODUCT VIEW or ADD TO CART PAGE */
            if ($request->getModuleName() == 'catalog' && $request->getControllerName() == 'product' && $request->getActionName() == 'view') {
                
                $product = Mage::getSingleton('checkout/session')->getData('adform.basket.product.product');
                $qty = Mage::getSingleton('checkout/session')->getData('adform.basket.product.qty');
                $redirectToCart = Mage::helper('checkout/cart')->getShouldRedirectToCart();
                
                if ($product && $qty && !$redirectToCart) {
                    
                    $currentProduct = Mage::getModel('catalog/product')
                                    ->load($product);

                    if ($currentProduct && $currentProduct->getId()) {
                        if (Mage::registry('current_category')) {
                            $currentCategory = Mage::registry('current_category');
                        } else {
                            $categoryIds = $currentProduct->getCategoryIds();

                            $currentCategory = Mage::getModel('catalog/category')
                                                    ->load($categoryIds[0]);
                        }

                        if ($currentCategory && $currentCategory->getId() && $currentProduct->getId()) {
                            $params = array(
                                //'productid' => $currentProduct->getId(),
                                'productid' => $currentProduct->getName(),
                                'productname' => $currentProduct->getName(),
                                //'productcount' => $qty,
                                //'categoryid' => $currentCategory->getId(),
                                'categoryid' => $currentCategory->getName(),
                                'categoryname' => $currentCategory->getName(),
                                'weight' => '5',
                                'step' => '2'
                            );

                            $params = $this->_helper->getUtf8CleanJsonArray($params);

                            $js->setText($js->getText().sprintf('adf.addProduct(%s);', $params));                        
                        }
                    }
                    
                    Mage::getSingleton('checkout/session')->unsetData('adform.basket.product.product');
                    Mage::getSingleton('checkout/session')->unsetData('adform.basket.product.qty');                    
                } 
                else {
                    if (($currentProduct = Mage::registry('current_product'))) {
                        $currentCategory = null;

                        if (Mage::registry('current_category')) {
                            $currentCategory = Mage::registry('current_category');
                        } else {
                            $categoryIds = $currentProduct->getCategoryIds();
                            if (isset($categoryIds[0])) {
                                $currentCategory = Mage::getModel('catalog/category')
                                    ->load($categoryIds[0]);
                            }
                        }

                        if ($currentCategory && $currentCategory->getId() && $currentProduct->getId()) {

                            $params = array(
                                //'productid' => $currentProduct->getId(),
                                'productid' => $currentProduct->getName(),
                                //'productname' => $currentProduct->getName(),
                                //'categoryid' => $currentCategory->getId(),
                                'categoryid' => $currentCategory->getName(),
                                //'categoryname' => $currentCategory->getName(),
                                'weight' => '5',
                                'step' => '1'
                            );

                            $params = $this->_helper->getUtf8CleanJsonArray($params);

                            $js->setText($js->getText().sprintf('adf.addProduct(%s);', $params));
                        }
                    }                     
                }
            }
            /* END Handle the PRODUCT VIEW or ADD TO CART PAGE */
            
            
            /* START Handle the CHECKOUT CART */
            if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'cart' && $request->getActionName() == 'index') {
                
                $product = Mage::getSingleton('checkout/session')->getData('adform.basket.product.product');
                $qty = Mage::getSingleton('checkout/session')->getData('adform.basket.product.qty');
  
                $currentProduct = Mage::getModel('catalog/product')
                                ->load($product);
                
                if ($currentProduct && $currentProduct->getId()) {
                    if (Mage::registry('current_category')) {
                        $currentCategory = Mage::registry('current_category');
                    } else {
                        $categoryIds = $currentProduct->getCategoryIds();
                        
                        //$currentCategory = Mage::getModel('catalog/category')
                        //                        ->load($categoryIds[0]);
                        if (isset($categoryIds[0])) {
                            $currentCategory = Mage::getModel('catalog/category')
                                                ->load($categoryIds[0]);
                            $currentCategoryName = $currentCategory->getName();
                        } else {
                            $currentCategoryName = null;
                        }
                    }
                    
//                    if ($currentCategory && $currentCategory->getId() && $currentProduct->getId()) {
                        $params = array(
                            //'productid' => $currentProduct->getId(),
                            'productid' => $currentProduct->getName(),
                            //'productname' => $currentProduct->getName(),
                            //'productcount' => $qty,
                            //'categoryid' => $currentCategory->getId(),
                            //'categoryid' => $currentCategory->getName(),
                            //'categoryname' => $currentCategory->getName(),
                            'weight' => '5',
                            'step' => '2'
                        );
                        if ($currentCategoryName) {
                            $params['categoryid'] = $currentCategoryName;
                        }

                        $params = $this->_helper->getUtf8CleanJsonArray($params);

                        $js->setText($js->getText().sprintf('adf.addProduct(%s);', $params));                        
//                    }
                } 
                
                Mage::getSingleton('checkout/session')->unsetData('adform.basket.product.product');
                Mage::getSingleton('checkout/session')->unsetData('adform.basket.product.qty');
            }
            /* END Handle the CHECKOUT CART PAGE */            
            
            
            
            /* START Handle the CHECKOUT ONEPAGE SUCCESS PAGE */
            if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'onepage' && $request->getActionName() == 'success') {
                
                $lastOrderId = Mage::getSingleton('checkout/type_onepage')->getCheckout()->getLastOrderId();
                $order = Mage::getModel('sales/order')->load($lastOrderId);
                
                $params = array(
                    'sales' => number_format($order->getGrandTotal(), 4),
                    'country' => Mage::helper('adform_track/comviq')->getCountry(),
                    'agegroup' => Mage::helper('adform_track/comviq')->getAgeGroup(),
                    'currency' => $order->getOrderCurrencyCode(),
                    'gender' => Mage::helper('adform_track/comviq')->getGenderFromSsn(),
                    'basketsize' => (int)$order->getTotalQtyOrdered(),
                    //'orderid' => $order->getIncrementId(),
                    'orderid' => $order->getIncrementId(),
                    //'step' => '3'
                );
                
                if (($customerAge = $this->_helper->getCustomerAge())) {
                    $params['agegroup'] = (string)$customerAge;
                }
                
                if (($customerGender = $this->_helper->getCustomerGender())) {
                    $params['gender'] = $customerGender;
                }
                Mage::log($params, null, 'adformparams_0.log', true);
                /*
                if (($address = $order->getBillingAddress())) {
                    $params['email'] = $address->getEmail();
                    $params['firstname'] = $address->getFirstname();
                    $params['lastname'] = $address->getLastname();
                    
                    if (($s1 = $address->getStreet1())) {
                        $params['address1'] = $s1;
                    } if (($s2 = $address->getStreet2())) {
                        $params['address2'] = $s2;
                    }
                    
                    $params['phone'] = $address->getTelephone();
                    $params['zip'] = $address->getPostcode();
                }
                */
                
                $params = $this->_helper->getUtf8CleanJsonArray($params);
                Mage::log($params, null, 'adformparams.log', true);

                $js->setText($js->getText().sprintf('adf.createOrder(%s);', $params));                 
                
                Mage::helper('adform_track/comviq')->prepareOfferValues($order->getAllItems());
                foreach ($order->getAllItems() as $item){
                    $currentProduct = Mage::getModel('catalog/product')
                                    ->load($item->getProductId());

                    if ($currentProduct && $currentProduct->getId()) {
                        if (Mage::registry('current_category')) {
                            $currentCategory = Mage::registry('current_category');
                            $currentCategoryName = $currentCategory->getName();
                        } else {
                            $categoryIds = $currentProduct->getCategoryIds();

                            if (isset($categoryIds[0])) {
                                $currentCategory = Mage::getModel('catalog/category')
                                                    ->load($categoryIds[0]);
                                $currentCategoryName = $currentCategory->getName();
                            } else {
                                $currentCategoryName = null;
                            }
                        }
                        
                        if (!Mage::helper('adform_track/comviq')->checkDevice($currentProduct)) {
                            continue;
                        }

                        //if ($currentCategory && $currentCategory->getId() && $currentProduct->getId()) {
                            $params = array(
                                //'productid' => $currentProduct->getId(),
                                'productid' => $currentProduct->getName(),
                                'productname' => $currentProduct->getName(),
                                'productcount' => (int)$item->getQtyOrdered(),
                                'productsales' => number_format($item->getRowTotal(), 4),
                                //'categoryid' => $currentCategory->getId(),
                                'sv10:' => $currentProduct->getName(),
                                'weight' => '5',
                                'step' => '3'
                            );

                            if ($currentCategoryName) {
                                $params['categoryid'] = $currentCategoryName;
                                $params['categoryname'] = $currentCategoryName;
                            }
                            if ($_phoneNumber = Mage::helper('adform_track/comviq')->getPhoneNumber($item)) {
                                $params['sv8'] = $_phoneNumber;
                            }
                            if ($offerValue = Mage::helper('adform_track/comviq')->getOfferValue($item)) {
                                $params['sv1'] = $offerValue;
                            }

                            $params = $this->_helper->getUtf8CleanJsonArray($params);

                            $js->setText($js->getText().sprintf('adf.addProduct(%s);', $params));                        
                        //}
                    } 
                }
            }
            /* END Handle the CHECKOUT ONEPAGE SUCCESS PAGE */
            
            if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'onepage' && $request->getActionName() == 'index') {
                //THESE ARE HANDLED TROUGH OTHER FUNCTIONS/OBSERVERS, etc...
            } else {
                $js->setText($js->getText().sprintf('adf.track(%s);', (int)$campaignId));

                $js->setText($js->getText().'</script>');                
                
                $js->setText($js->getText().
                    sprintf('<noscript><p style="margin:0;padding:0;border:0;"><img src="%s?pm=%s&ADFPageName=%s&ADFdivider=%s" width="1" height="1" alt="" /></p></noscript>', $trackPointImgUrl, $campaignId, urlencode(html_entity_decode($title)), $dividerChar)
                );

                $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END -->'));                
            }
            
            $observer->getEvent()->getBlock()->insert($js);            
        }
        
        return $this;
    }
    
    public function injectCheckoutFirstStepTrackingLogicJs($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            /* START ONEPAGE_CHECKOUT_STEP_BILLING */
            if ($observer->getEvent()->getBlock()->getNameInLayout() === self::ONEPAGE_CHECKOUT_STEP_BILLING) {

                $js = Mage::app()->getLayout()
                            ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_BILLING');

                $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_BILLING -->'));
                $js->setText($js->getText().'<script type="text/javascript">');          
                $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Billing Information'), $this->_helper->getDividerChar()));
                $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
                $js->setText($js->getText().'</script>');
                
                $js->setText($js->getText().
                    sprintf('<noscript><p style="margin:0;padding:0;border:0;"><img src="%s?pm=%s&ADFPageName=%s&ADFdivider=%s" width="1" height="1" alt="" /></p></noscript>', $this->_helper->getTrackPointImgUrl(), (int)$this->_helper->getCampaignId(), $this->_helper->getPreparedCheckoutPageTitle('Billing Information'), $this->_helper->getDividerChar())
                );                
                
                $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_BILLING -->'));
 
                $observer->getEvent()->getTransport()->setHtml(
                    $observer->getEvent()->getTransport()->getHtml().$js->toHtml()
                );
            }
            /* END ONEPAGE_CHECKOUT_STEP_BILLING */            
        } else {
            /* START ONEPAGE_CHECKOUT_STEP_LOGIN, when checkout as GUEST */
            if ($observer->getEvent()->getBlock()->getNameInLayout() === self::ONEPAGE_CHECKOUT_STEP_LOGIN) {
                
                $js = Mage::app()->getLayout()
                            ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_BILLING');

                $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_LOGIN -->'));
                $js->setText($js->getText().'<script type="text/javascript">');
                $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Checkout Method'), $this->_helper->getDividerChar()));
                $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
                $js->setText($js->getText().'document.observe("dom:loaded", function() {');
                $js->setText($js->getText().'$("onepage-guest-register-button").observe("click", function(event) {');
                $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Billing Information'), $this->_helper->getDividerChar()));
                $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
                $js->setText($js->getText().'});');
                
                /* Check injectShippingInfoTrackingLogicJs() */
                //$js->setText($js->getText().'$("checkout-step-billing").select("button").each(function(el){');
                //    $js->setText($js->getText().'$(el).observe("click", function(event) {');
                //        $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Shipping Information'), $this->_helper->getDividerChar()));
                //        $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
                //    $js->setText($js->getText().'});');
                //$js->setText($js->getText().'});');
                
                $js->setText($js->getText().'});');
                $js->setText($js->getText().'</script>');

                
                
                
                
                $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_LOGIN -->'));
 
                $js->setText($js->getText().
                    sprintf('<noscript><p style="margin:0;padding:0;border:0;"><img src="%s?pm=%s&ADFPageName=%s&ADFdivider=%s" width="1" height="1" alt="" /></p></noscript>', $this->_helper->getTrackPointImgUrl(), (int)$this->_helper->getCampaignId(), $this->_helper->getPreparedCheckoutPageTitle('Billing Information'), $this->_helper->getDividerChar())
                );                    
                
                $observer->getEvent()->getTransport()->setHtml(
                    $observer->getEvent()->getTransport()->getHtml().$js->toHtml()
                );
            }
            /* END ONEPAGE_CHECKOUT_STEP_LOGIN */              
        }      
        
        return $this;
    }
    
    public function injectSaveBillingTrackingLogicJs($observer)
    {   
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        $responseBody = $observer->getEvent()->getControllerAction()
                                ->getResponse()->getBody();
        
        $responseBody = json_decode((string)$responseBody);
        
        if ($responseBody->goto_section == 'shipping_method') {
            
            $js = Mage::app()->getLayout()
                        ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD');

            $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD -->'));
            $js->setText($js->getText().'<script type="text/javascript">');          
            $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Shipping Method'), $this->_helper->getDividerChar()));
            $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
            $js->setText($js->getText().'</script>');
            $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD -->'));
            
            $responseBody->update_section->html = $responseBody->update_section->html . $js->getText();
        }
        
        $responseBody = json_encode($responseBody);
        
        $observer->getEvent()->getControllerAction()
                                ->getResponse()->setBody($responseBody);

    }
    
    public function injectShippingInfoTrackingLogicJs($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        if ($observer->getEvent()->getBlock()->getNameInLayout() === self::ONEPAGE_CHECKOUT_STEP_SHIPPING) {

            $js = Mage::app()->getLayout()
                            ->createBlock('Mage_Core_Block_Text', 'adform_track_ONEPAGE_CHECKOUT_STEP_SHIPPING');  

            $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_SHIPPING -->'));
            $js->setText($js->getText().sprintf('<script type="text/javascript">var adformFireOnce = false; $("co-shipping-form").observe("mouseover", function(){ if (adformFireOnce == false) { adf.Params.PageName=encodeURIComponent("%s"); adf.Params.Divider=encodeURIComponent("%s"); adf.track(%s); } adformFireOnce = true; });</script>', $this->_helper->getPreparedCheckoutPageTitle('Shipping Information'), $this->_helper->getDividerChar(), (int)$this->_helper->getCampaignId()));
            $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_SHIPPING -->'));

            $observer->getEvent()->getTransport()->setHtml(
                $observer->getEvent()->getTransport()->getHtml().$js->toHtml()
            );
        }
    }    
    
    public function injectSaveShippingTrackingLogicJs($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        $responseBody = $observer->getEvent()->getControllerAction()
                                ->getResponse()->getBody();
        
        $responseBody = json_decode((string)$responseBody);
        
        if ($responseBody->goto_section == 'shipping_method') {
            $js = Mage::app()->getLayout()
                        ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD');

            $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD -->'));
            $js->setText($js->getText().'<script type="text/javascript">');          
            $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Shipping Method'), $this->_helper->getDividerChar()));
            $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
            $js->setText($js->getText().'</script>');
            $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_SHIPPING_METHOD -->'));
            
            $responseBody->update_section->html = $responseBody->update_section->html . $js->getText();
        }
        
        $responseBody = json_encode($responseBody);
        
        $observer->getEvent()->getControllerAction()
                                ->getResponse()->setBody($responseBody);

    }
    
    public function injectSaveShippingMethodTrackingLogicJs($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        $responseBody = $observer->getEvent()->getControllerAction()
                                ->getResponse()->getBody();
        
        $responseBody = json_decode((string)$responseBody);
        
        if ($responseBody->goto_section == 'payment') {
            $js = Mage::app()->getLayout()
                        ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_PAYMENT_INFO');

            $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_PAYMENT_INFO -->'));
            $js->setText($js->getText().'<script type="text/javascript">');          
            $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Payment Information'), $this->_helper->getDividerChar()));
            $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
            $js->setText($js->getText().'</script>');
            $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_PAYMENT_INFO -->'));
            
            $responseBody->update_section->html = $responseBody->update_section->html . $js->getText();
        }
        
        $responseBody = json_encode($responseBody);
        
        $observer->getEvent()->getControllerAction()
                                ->getResponse()->setBody($responseBody);

    }
    
    public function injectSavePaymentTrackingLogicJs($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        $responseBody = $observer->getEvent()->getControllerAction()
                                ->getResponse()->getBody();
        
        $responseBody = json_decode((string)$responseBody);
        
        if ($responseBody->goto_section == 'review') {
            $js = Mage::app()->getLayout()
                        ->createBlock('Mage_Core_Block_Text', 'adform_track_js_ONEPAGE_CHECKOUT_STEP_ORDER_REVIEW');

            $js->setText($this->_helper->__('<!-- Adform Tracking Code BEGIN ONEPAGE_CHECKOUT_STEP_ORDER_REVIEW -->'));
            $js->setText($js->getText().'<script type="text/javascript">');          
            $js->setText($js->getText().sprintf('adf.Params.PageName=encodeURIComponent("%s");adf.Params.Divider=encodeURIComponent("%s");', $this->_helper->getPreparedCheckoutPageTitle('Order Review'), $this->_helper->getDividerChar()));
            $js->setText($js->getText().sprintf('adf.track(%s);', (int)$this->_helper->getCampaignId()));
            $js->setText($js->getText().'</script>');
            $js->setText($js->getText().$this->_helper->__('<!-- Adform Tracking Code END ONEPAGE_CHECKOUT_STEP_ORDER_REVIEW -->'));
            
            $responseBody->update_section->html = $responseBody->update_section->html . $js->getText();
        }
        
        $responseBody = json_encode($responseBody);
        
        $observer->getEvent()->getControllerAction()
                                ->getResponse()->setBody($responseBody);

    }     
    
    public function recordLastAddedProduct($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        
        Mage::getSingleton('checkout/session')
                ->setData('adform.basket.product.product', $request->getParam('product'));
        
        Mage::getSingleton('checkout/session')
                ->setData('adform.basket.product.qty', $request->getParam('qty'));
        
        return $this;
    }
    
    public function injectProductGridFeedGenerateAction($observer)
    {
        if (!$this->_helper->isModuleEnabled() OR !$this->_helper->isModuleOutputEnabled()) {
            return $this;
        }
        
        //if ((Mage::app()->getRequest()->getControllerName() == 'catalog_product') && Mage::app()->getRequest()->getActionName() == 'index') {
        if (preg_match("#catalog_(.*)#", Mage::app()->getRequest()->getControllerName())) {
            if ($observer->getEvent()->getBlock()->getType() == 'adminhtml/widget_grid_massaction') {
                $observer->getEvent()->getBlock()->addItem('adform_generate_feed', array(
                    'label'=> Mage::helper('adform_track')->__('Generate Adform XML Feed'),
                    'url'  =>   Mage::helper('adminhtml')->getUrl('*/adform_feed/generate', array('_current'=>true)),
                    'confirm' => Mage::helper('adform_track')->__('Are you sure?')
                )); 
            }            
        }
    }
}
