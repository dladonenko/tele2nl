<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_XML_PATH_ADFORM_TRACK_ACTIVE = 'adform_track/settings/active';
    const CONFIG_XML_PATH_ADFORM_TRACK_CAMPAIGN_ID = 'adform_track/settings/campaign_id';
    const CONFIG_XML_PATH_ADFORM_TRACK_USE_CUSTOMER_AGE_INFO = 'adform_track/settings/use_customer_age_info';
    const CONFIG_XML_PATH_ADFORM_TRACK_USE_CUSTOMER_GENDER_INFO = 'adform_track/settings/use_customer_gender_info';
    const CONFIG_XML_PATH_ADFORM_TRACK_JS_LIB_URL = 'adform_track/settings/js_lib_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_JS_SECURE_LIB_URL = 'adform_track/settings/js_secure_lib_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_DIVIDER_CHAR = 'adform_track/settings/divider_char';
    const CONFIG_XML_PATH_ADFORM_TRACK_TRACK_POINT_IMG_URL = 'adform_track/settings/track_point_img_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_TRACK_POINT_IMG_SECURE_URL = 'adform_track/settings/track_point_img_secure_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_ENCRYPT_DECRYPT_WEBSERVICE_URL = 'adform_track/settings/encrypt_decrypt_webservice_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_ENCRYPT_DECRYPT_WEBSERVICE_SECURE_URL = 'adform_track/settings/encrypt_decrypt_webservice_secure_url';
    const CONFIG_XML_PATH_ADFORM_TRACK_PRODUCT_PAGE_TRACKING_LEVEL = 'adform_track/settings/product_page_tracking_level';
    
    const CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_WIDTH = 'adform_track/feed/image_width';
    const CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_HEIGHT = 'adform_track/feed/image_height';
    const CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_PPF = 'adform_track/feed/ppf';
    const CONFIG_XML_PATH_ADFORM_TRACK_FEED_FILE_REFRESH_INTERVAL = 'adform_track/feed/file_refresh_interval';
    
    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    
    public function isModuleEnabled($moduleName = null)
    {
        if (Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_ACTIVE) == '0') {
            return false;
        }
        
        return parent::isModuleEnabled($moduleName = null);
    }
    
    public function isModuleOutputEnabled($moduleName = null)
    {
        if (!$this->getCampaignId()) {
            return false;
        }
        
        return parent::isModuleOutputEnabled($moduleName);
    }
    
    public function getCampaignId()
    {
        /* config client id */
        $ccid = Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_CAMPAIGN_ID);
        
        /* decrypted client id */
        $dcid = Mage::getSingleton('core/session')->getData('adform_dcid');
        
        if (!empty($ccid) && $dcid) {
            return $dcid;
        } else {
            $dcid = file_get_contents(sprintf('%s?key=%s', $this->getEncryptDecryptWebserviceUrl(), $ccid));
            if (!empty($dcid)) {
                Mage::getSingleton('core/session')->setData('adform_dcid', $dcid);
            } else {
                return false;
            }
        }
        
        return Mage::getSingleton('core/session')->getData('adform_dcid');
    } 
    
    public function getEncryptDecryptWebserviceUrl()
    {
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            return $this->_getEncryptDecryptWebserviceSecureUrl();
        }
        
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_ENCRYPT_DECRYPT_WEBSERVICE_URL);
    }
    
    private function _getEncryptDecryptWebserviceSecureUrl()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_ENCRYPT_DECRYPT_WEBSERVICE_SECURE_URL);        
    }    

    public function getUseCustomerAgeInfo()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_USE_CUSTOMER_AGE_INFO);
    }    
    
    public function getUseCustomerGenderInfo()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_USE_CUSTOMER_GENDER_INFO);
    }

    public function getCustomerAge($customer = null)
    {
        if ($this->getUseCustomerAgeInfo()) {
            
            if ($customer instanceof Mage_Customer_Model_Customer) {
                if ($customer->getDob()) {
                    return (int)$this->_getAge($customer->getDob());
                }
            } elseif(is_numeric($customer)) {
                $customer = Mage::getModel('customer/customer')
                                ->load($customer);
                
                if ($customer && $customer->getId()) {
                    if ($customer->getDob()) {
                        return (int)$this->_getAge($customer->getDob());
                    }
                }
            } else {
                if (($customer = Mage::getSingleton('customer/session')->getCustomer())) {
                    if ($customer->getDob()) {
                        return (int)$this->_getAge($customer->getDob());
                    }
                }
            }
        }
        
        return 0;
    }
    
    public function getCustomerGender($customer = null)
    {
        if ($this->getUseCustomerGenderInfo()) {
            if (!$customer) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();            
            }

            if ($customer && $customer->getId()) {
                /* Older versions of Magento, 1.6- */
                if ($customer->getGender()) {
                    if ($customer->getGender() == '1') {
                        return self::GENDER_MALE;
                    } if ($customer->getGender() == '2') {
                        return self::GENDER_FEMALE;
                    }   
                }

                /* Newer versions of Magento, 1.7+ */
                try {
                    $genderText = $customer->getResource()
                                            ->getAttribute('gender')
                                                ->getSource()
                                                    ->getOptionText($customer->getData('gender'));

                    if (strtolower($genderText) === 'male') {
                        return self::GENDER_MALE;
                    }                

                    if (strtolower($genderText) === 'female') {
                        return self::GENDER_FEMALE;
                    }

                    return $genderText;
                } catch (Exception $e) {
                    Mage::logException($e);
                }            
            }            
        }
        
        return 0;
    }  
    
    private function _getAge($birthday)
    {
        $birthday = explode(" ", $birthday);
        $birthday = $birthday[0];
        
        list($year,$month,$day) = explode("-",$birthday);
        
        $year_diff  = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff   = date("d") - $day;
        
        if ($day_diff < 0 || $month_diff < 0) {
            $year_diff--;
        }
        
        return $year_diff;
    }
    
    public function getJsLibUrl()
    {
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_JS_SECURE_LIB_URL);
        }
        
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_JS_LIB_URL);
    } 
    
    public function getDividerChar()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_DIVIDER_CHAR);
    }
    
    public function getTrackPointImgUrl()
    {
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_TRACK_POINT_IMG_SECURE_URL);
        }
        
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_TRACK_POINT_IMG_URL);
    }
    
    public function getStoreName()
    {
        return Mage::app()->getStore()->getName();        
    }
    
    public function getPreparedPageTitle($title)
    {
        $request = Mage::app()->getRequest();
        
        if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'onepage' && $request->getActionName() == 'success') {
            $dividerChar = $this->getDividerChar();

            $title = implode($dividerChar, array($this->getStoreName(), $this->__('Checkout'), $this->__('Confirmation'), $this->__('Thank You')));

            return $title;
        }         
        
        $dividerChar = $this->getDividerChar();
        
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', Mage::app()->getStore());
        $separator = ' ' . $separator . ' ';
        
        $title = str_replace($separator, $dividerChar, $title);        
        $title = explode($dividerChar, $title);
        $title = array_reverse($title);

        if ($this->getProductPageTrackingLevel() === Adform_Track_Model_Config_Product_Page_Tracking_Level::CATEGORY) {
            if ($request->getModuleName() == 'catalog' && $request->getControllerName() == 'product' && $request->getActionName() == 'view') {
                $currentProduct = Mage::registry('current_product');

                if ($currentProduct && $currentProduct->getId()) {
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
                    
                    //Zend_Debug::dump($currentCategory->debug()); exit;

                    if ($currentCategory && $currentCategory->getId() && $currentProduct->getId()) {
                        //Mage::log($currentCategory->debug(), null, 'dddddd.log', true);
                        
                        $pathInStore = $currentCategory->getPath();
                        $cats = explode('/', $pathInStore); /* first item in array is $currentCategory */
                        array_shift($cats); /* Remove first item from array, category with ID 1 */
                        array_shift($cats); /* Remove second item from array, store root category */
                        
                        $paths = array();                        
                        $paths[] = $this->getStoreName();
                        
                        foreach($cats as $cat) {
                            
                            if ($cat == end($cats)) {
                                $paths[] = 'Product category: '.Mage::getModel('catalog/category')->load($cat)->getName();
                            } else {
                                $paths[] = Mage::getModel('catalog/category')->load($cat)->getName();
                            }
                        }
                        
                        
                        
                        $title = implode($dividerChar, $paths);
                        return $title;
                    }
                }
            }            
        }        
        
        if ($request->getModuleName() == 'catalogsearch' && $request->getControllerName() == 'result' && $request->getActionName() == 'index') {
            $title = implode($dividerChar, array($this->getStoreName(), $this->__('Search Results')));
            return $title;
        }
        
        if ($request->getModuleName() == 'catalogsearch' && $request->getControllerName() == 'advanced' && $request->getActionName() == 'result') {
            $title = implode($dividerChar, array($this->getStoreName(), $this->__('Advanced Search Results')));
            return $title;
        }        
        
        array_unshift($title, $this->getStoreName());

        $title = implode($dividerChar, $title);
        
        return $title;
    }
    
    public function getPreparedCheckoutPageTitle($stepName)
    {
        $dividerChar = $this->getDividerChar();

        $title = implode($dividerChar, array($this->getStoreName(), $this->__('Checkout'), $this->__('Step'), $this->__($stepName)));
        
        return $title;
    }    
    
    public function getFeedImageWidth()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_WIDTH);
    }
    
    public function getFeedImageHeight()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_HEIGHT);
    }
    
    public function getFeedPpf()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_FEED_IMAGE_PPF);
    }    
    
    public function getFeedFileRefreshInterval()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_FEED_FILE_REFRESH_INTERVAL);
    }
    
    public function getShopLogoImageUrl($imgW, $imgH)
    {
        /* Url path */
        $logoSrc = Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
        $_logoSrc = explode('.', $logoSrc);
        $logoImgExt = array_pop($_logoSrc);
        
        /* Abs path */
        $logoSrcAbsPath = BP.DS.'skin'.DS.array_pop($_logoSrc);
        
        $logoSrcResizedAbsPath = Mage::getBaseDir('media').DS.'__shop_logo_img__.'.$logoImgExt;
        
        try {
            $imageObj = new Varien_Image($logoSrcAbsPath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize($imgW, $imgH);
            $imageObj->save($logoSrcResizedAbsPath);

            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'__shop_logo_img__.'.$logoImgExt;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return '';
    }
    
    
    public function getUtf8CleanJsonArray($arr)
    {
        array_walk_recursive($arr, function(&$item, $key) {
            if(is_string($item)) {
                $item = htmlentities($item, ENT_NOQUOTES);
            }
        });

        $json = json_encode($arr);
        $rson = html_entity_decode($json);        
        
        return $rson;
    }
    
    public function getUtf8CleanJsonStringFromHtmlString($str)
    {
        return html_entity_decode(json_encode(htmlentities(html_entity_decode($str), ENT_NOQUOTES)));
    }
    
    public function getProductPageTrackingLevel()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ADFORM_TRACK_PRODUCT_PAGE_TRACKING_LEVEL);
    }
}
