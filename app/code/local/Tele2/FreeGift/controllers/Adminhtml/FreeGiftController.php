<?php

class Tele2_FreeGift_Adminhtml_FreeGiftController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Teleco'))
            ->_title($this->__('Free Gifts'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function bindingAction()
    {
        $subscripionId = trim($this->getRequest()->getParam('subscription_id'));
        $bindingData = array();
        if ($subscripionId) {
            $subscription = Mage::getModel('tele2_subscription/subscription')->load($subscripionId);
            $bindings = $subscription->getBindings();
            foreach ($bindings as $binding) {
                $bindingData[] = array(
                    'value' => $binding->getBindingId(),
                    'label' => $binding->getTime()
                );
                
            }
        }
        $result = array(
            'binding' => $bindingData
        );
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getBindingInfoHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_comviq_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }

    /**
     * Subscription grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Teleco'))
            ->_title($this->__('Free Gifts'));

        
        $freeGiftId = $this->getRequest()->getParam('entity_id');
        $freeGift = Mage::getModel('tele2_freeGift/freeGift');

        if ($freeGiftId) {
            $freeGift->load($freeGiftId);

            if (!$freeGift->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_freeGift')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($freeGift->getId() ? $freeGift->getName() : $this->__('New Free Gift'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $freeGift->setData($data);
        }

        Mage::register('freeGift', $freeGift);

        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('data')) {
  
            //init subscription model and set data
            $freeGift = Mage::getModel('tele2_freeGift/freeGift');
            
            if (isset($data['entity_id'])) {
                $freeGift->load($data['entity_id']);
            }

            if (isset($data['condition_binding_period'])) {
                $data['condition_binding_period'] = (string)implode(',', $data['condition_binding_period']);
            } else {
                $data['condition_binding_period'] = '';
            }

            $actionProductIds = array();
            if (isset($data['action_addon_id'])) {
                $actionProductIds = array_merge($actionProductIds, $data['action_addon_id']);
            }
            if (isset($data['action_accessory_id'])) {
                $actionProductIds = array_merge($actionProductIds, $data['action_accessory_id']);
            }
            if (isset($data['action_device_id'])) {
                $actionProductIds = array_merge($actionProductIds, $data['action_device_id']);
            }
            $data['action_product_id'] = (string)implode(',', $actionProductIds);

            $freeGift->setData($data);
            // try to save it
            try {
                // save the data
                $freeGift->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_freeGift')->__('The freegift has been saved.')
                );
                // clear previously saved data from session

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('entity_id' => $freeGift->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/freegift');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('tele2_freegift')->__('An error occurred while saving the freegift.'));
            }

            if ($freeGift->getId()) {
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('entity_id' => $freeGift->getId()));
            }
            return;
        }
        $this->_redirect('*/freegift');
    }
    public function deleteAction()
    {
        $freeGiftId = $this->getRequest()->getParam('entity_id');
        if ($freeGiftId) {
            $freeGift = Mage::getModel('tele2_freeGift/freeGift')
                ->load($freeGiftId)
                ->delete();
            // display success message
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('tele2_freeGift')->__('The freegift has been delted.')
            );
        }
        // go to grid
        $this->_redirect('*/freegift');
        return;
    }
}

