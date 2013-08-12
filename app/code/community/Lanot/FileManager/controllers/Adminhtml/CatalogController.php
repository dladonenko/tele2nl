<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Lanot
 * @package     Lanot_FileManager
 * @copyright   Copyright (c) 2012 Lanot
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once('Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php');

class Lanot_FileManager_Adminhtml_CatalogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get related online documents
     */
    public function onlineDocumentsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_catalog_product_online')
            ->setOnlineDocuments($this->getRequest()->getPost('online_documents', null));
        $this->renderLayout();
    }

    /**
     * Initialize Subscription from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Related Online Documents'))
            ->_title($this->__('Manage Content'));

        $productId = $this->getRequest()->getParam('product_id');
        $product = Mage::getModel('catalog/product');

        if ($productId) {
            $product->load($productId);

            if (! $product->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('lanot_filemanager')->__('This page no longer exists.'));
                $this->_redirect('*/*/index');
                return;
            }
        }

        $this->_title($product->getId() ? $product->getName() : $this->__('New Product'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $product->setData($data);
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);

        return $product;
    }

    public function onlinedocumentsgridAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $product = Mage::getModel('catalog/product')->load($productId);

        Mage::register('product', $product);

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('lanot_filemanager/adminhtml_catalog_product_online_grid')
                ->toHtml()
        );
    }
}
