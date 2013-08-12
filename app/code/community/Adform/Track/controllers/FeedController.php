<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_FeedController extends Mage_Core_Controller_Front_Action
{
    /* http://magento1620.ce/index.php/adform/feed/list/key/some_cool_key.xml */
    public function listAction()
    {
        $urlKey = $this->getRequest()->getParam('key', false);
        $urlKey = strtolower($urlKey);
        $urlKey = str_replace('.xml', '', $urlKey);
        
        $helper = Mage::helper('adform_track');
        
        ini_set('max_execution_time', '0');
        $exTime = ini_get('max_execution_time');
        
        $currentTime = microtime();
        $loopAllowedTime = $exTime - 5;
        
        
        if (!$urlKey) {
            exit($helper->__('Url key missing!'));
        }
        
        
        $feed = Mage::getModel('adform_track/feed');
        $feed->load($urlKey, 'url_key');
        
        if ($feed && $feed->getId()) {
            
            if (!file_exists(BP.DS.'var'.DS.'adform_feed')) {
                mkdir(BP.DS.'var'.DS.'adform_feed');
            }
            
            $filename = BP.DS.'var'.DS.'adform_feed'.DS.$urlKey.'.xml';
            
            if (file_exists($filename)) {
                
                if ((time() - filemtime($filename)) < ((int)$helper->getFeedFileRefreshInterval() * 60)) {
                    $fp = fopen($filename, 'rb');

                    // send the right headers
                    header("Content-Type: text/xml");
                    header("Content-Length: " . filesize($filename));

                    // dump the picture and stop the script
                    fpassthru($fp);
                    exit;
                }
            }
            
            $store = Mage::app()->getStore($feed->getStoreId());
            
            $collection = Mage::getModel('catalog/product')->getCollection();

            if ($store->getId()) {
                $collection->setStoreId($feed->getStoreId());
                
                $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
                $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());                
            } else {
                $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
                $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');                
            }
            

            
            $collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            $collection->addAttributeToFilter('visibility', array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH));
            
            
            if ($feed->getSelectionType() == 'selected') {
                $products = explode(',', $feed->getProducts());
                $collection->addAttributeToFilter('entity_id', array('in'=>$products));
            }
            
            $collection->getSelect()->order('entity_id DESC');
            //$collection->getSelect()->limit((int)$helper->getFeedPpf());
            //$collection->setPageSize((int)$helper->getFeedPpf());

            $products = $collection->getAllIds((int)$helper->getFeedPpf());
            
            $shopLogoImageUrl = $helper->getShopLogoImageUrl($helper->getFeedImageWidth(), $helper->getFeedImageHeight());
            
            /**
             * XMLWriter, This extension is enabled by default
             * http://www.php.net/manual/en/xmlwriter.installation.php
             */
            $writer = new XMLWriter();
            $writer->openURI($filename);
            $writer->startDocument('1.0', 'UTF-8');
            
            $writer->startElement('root');
            
            foreach ($products as $p) {
                
                if ($exTime != '0') {
                    if (((int)(microtime() - $currentTime)) > $loopAllowedTime) {
                        break;    
                    }
                }
                
                $product = Mage::getModel('catalog/product');
                if ($store->getId()) {
                    $product->setStoreId($store->getId());
                    $product->setStore($store);
                }
                $product->load($p);
                
                if (!$product->isSalable()) {
                    continue;
                }                
                
                if ($product && $product->getId()) {
                    //START Single Product Entry
                    $writer->startElement('product');
                        $writer->startElement('product_id');
                            $writer->text($product->getName());
                        $writer->endElement();
                        
                        $category = $product->getCategoryIds();
                        if (isset($category[0])) {
                            $writer->startElement('product_category_id');
                            $category = Mage::getModel('catalog/category')->load($category[0]);
                            $writer->text($category->getName());
                            $writer->endElement();
                            unset($category);
                        }
                        
                        $writer->startElement('product_name');
                            $writer->text($product->getName());
                        $writer->endElement();
                        
                        $writer->startElement('product_description');
                            $writer->text(substr(strip_tags($product->getShortDescription()), 0, 999));
                        $writer->endElement();
                        
                        $writer->startElement('product_price');
                            $writer->text(Mage::helper('core')->currency($product->getFinalPrice(), true, false));
                        $writer->endElement();

                        $writer->startElement('product_deeplink');
                            $writer->text($product->getProductUrl());
                        $writer->endElement();

                        $writer->startElement('product_image');
                            $writer->text(Mage::helper('catalog/image')->init($product, 'small_image')->resize($feed->getImageWidth(), $feed->getImageHeight()));
                        $writer->endElement();

                        $writer->startElement('shop_logo');
                            $writer->text($shopLogoImageUrl);
                        $writer->endElement();                        
                        
                        unset($product);
                        
                    $writer->endElement();
                    //END Single Product Entry                     
                }               
            }
                            
            $writer->endElement();
            
            $writer->endDocument();
            $writer->flush();
            
            if (file_exists($filename)) {
                $fp = fopen($filename, 'rb');

                // send the right headers
                header("Content-Type: text/xml");
                header("Content-Length: " . filesize($filename));

                // dump the picture and stop the script
                fpassthru($fp);
                exit;
            }             
            
            return;
        } else {
            exit($helper->__('Invalid url key!'));
        }        
        
        exit();
    }
}
