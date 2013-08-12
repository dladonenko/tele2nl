<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Adminhtml_Adform_FeedController extends Mage_Adminhtml_Controller_Action 
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('catalog/adform_track');

        $this->_addContent($this->getLayout()->createBlock('adform_track/adminhtml_edit'));

        $this->renderLayout();
    }
  
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adform_track/adminhtml_edit_grid')->toHtml()
        );
    }
    
    public function generateAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        
        $helper = Mage::helper('adform_track');
        
        $_conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $total = $_conn->fetchOne('SELECT COUNT(entity_id) FROM '.$_conn->getTableName('catalog_product_entity'));
        
        $feed = Mage::getModel('adform_track/feed');
        
        $feed->setStoreId($storeId);
        $feed->setUrlKey(time());
        $feed->setImageWidth($helper->getFeedImageWidth());
        $feed->setImageHeight($helper->getFeedImageHeight());
        $feed->setPpf($helper->getFeedPpf());
        
        if ($total == count($productIds)) {
            $feed->setSelectionType('all');
        } else {
            $feed->setSelectionType('selected');
            $feed->setProducts(implode(',', $productIds));
        }        
        
        try {
            $feed->save();
            Mage::getSingleton('core/session')->addSuccess($helper->__('Feed entry successfully saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/index');
        return;        
    }
    
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        $helper = Mage::helper('adform_track');
        
        if (!$id) {
            Mage::getSingleton('core/session')->addError($helper->__('Feed entry not found in the database.'));
            $this->_redirect('*/*/index');
            return;
        }
        
        $feed = Mage::getModel('adform_track/feed');
        $feed->load($id);
        
        if ($feed && $feed->getId()) {
            try {
                $feed->delete();
                
                Mage::getSingleton('core/session')->addSuccess($helper->__('Feed successfully deleted.'));
                $this->_redirect('*/*/index');
                
                return;                   
            } catch (Exception $e) {
                Mage::logException($e);                
                Mage::getSingleton('core/session')->addError($helper->__('System error, unable to delete the feed.'));
                $this->_redirect('*/*/index');
                
                return;                
            }
        } else {
            Mage::getSingleton('core/session')->addError($helper->__('System error, unable to load the feed.'));
            $this->_redirect('*/*/index');
            return;
        }
    }
}
