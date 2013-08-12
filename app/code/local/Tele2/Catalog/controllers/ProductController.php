<?php
require_once "app/code/core/Mage/Catalog/controllers/ProductController.php";
class Tele2_Catalog_ProductController extends Mage_Catalog_ProductController
{
    public function indexAction()
    {
        echo 'preview index';
    }

    /**
     * Product preview action
     */
    public function previewAction()
    {
        //die('OK');
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            // Prepare data
            $productHelper = Mage::helper('catalog/product');
            if (!$params) {
                $params = new Varien_Object();
            }

            // Standard algorithm to prepare and rendern product view page
            //$product = $productHelper->initProduct($productId, $this, $params);
            $product = Mage::getModel('catalog/product');
            $product->load($productId);
            //$product->getCategoryIds()

            $productData = $this->getRequest()->getParam('product');

            //$product->addData($newProductData);
            //$product->setData($productData);

            if ($productData && is_array($productData)) {
                $product->addData($productData);
            }

            $options = $product->getOptions();
            if ($options && isset($productData['options'])) {
                foreach ($options as $option) {
                    foreach ($option->getValues() as $value)
                    {
                        $value->setSku($productData['options'][$option->getId()]['values'][$value->getId()]['sku']);
                        $value->setTitle($productData['options'][$option->getId()]['values'][$value->getId()]['title']);
                        $value->setPrice($productData['options'][$option->getId()]['values'][$value->getId()]['price']);
                    }
                }
            }

            // Register current data and dispatch final events
            Mage::register('current_product', $product);
            Mage::register('product', $product);

            try {
                Mage::dispatchEvent('catalog_controller_product_init', array('product' => $product));
                Mage::dispatchEvent('catalog_controller_product_init_after',
                    array('product' => $product,
                        'controller_action' => $this
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                return false;
            }


            if (!$product) {
                throw new Mage_Core_Exception($this->__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
            }

            $buyRequest = $params->getBuyRequest();
            /*if ($buyRequest) {
                $productHelper->prepareProductOptions($product, $buyRequest);
            }*/

            if ($params->hasConfigureMode()) {
                $product->setConfigureMode($params->getConfigureMode());
            }

            //Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

            if ($params->getSpecifyOptions()) {
                $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                Mage::getSingleton('catalog/session')->addNotice($notice);
            }

            $viewHelper->initProductLayout($product, $this);

            $this->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'))
                ->renderLayout();

        } catch (Exception $e) {
            echo "Exception {$e->getMessage()}";
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }

    }
}