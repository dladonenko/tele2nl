<?php

class Tele2_Subscription_Adminhtml_GroupsController extends Mage_Adminhtml_Controller_Action
{
    public $model;
    
    protected function _construct()
    {
        $this->model = Mage::getModel('tele2_subscription/group');
    }
    
    public function groupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

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
        $this->_title($this->__('Group'))
             ->_title($this->__('Manage Content'));

        
        $groupId = $this->getRequest()->getParam('group_id');
        $group = Mage::getModel('tele2_subscription/group');

        if ($groupId) {
            $group->load($groupId);

            if (! $group->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($group->getId() ? $group->getName() : $this->__('New Page'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $group->setData($data);
        }

        Mage::register('subscription_group', $group);

        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
  
            if ($group_id = $this->getRequest()->getParam('group_id')) {
                $this->model->load($group_id);
            }

            $this->model->setData($data);

            // try to save it
            try {
                // save the data
                $this->model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('The page has been saved.'));
                // clear previously saved data from session

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('group_id' => $this->model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/group');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('tele2_subscription')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('group_id' => $this->getRequest()->getParam('group_id')));
            return;
        }
        $this->_redirect('*/*/group');
    }
    
    public function deleteAction()
    {
        $group_id = $this->getRequest()->getParam('group_id');
        if ($group_id) {
            try {
                $subscription = Mage::getModel('tele2_subscription/group')
                    ->load($group_id)
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('Group has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('Group has not been deleted.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/group');
    }
}

