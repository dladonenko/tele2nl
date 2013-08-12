<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */

class Tele2_Subscription_Adminhtml_SubscriptionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('mobile');
    }

    public function mobileAction()
    {
        $this->_title($this->__('Tele 2'))
          ->_title($this->__('Mobile Subscriptions'));

        $this->loadLayout();
        $this->_setActiveMenu('telco_freegift/subscriptions');
        $this->renderLayout();
    }

    /**
     * Subscription grid for AJAX request
     */
    public function mobileGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function mobileNewAction()
    {
        $this->_forward('edit');
    }
    
    public function mobileEditAction()
    {
        $this->_initMobileSubscription();

        $this->loadLayout();
        $this->_setActiveMenu('telco_freegift/subscriptions');

        $this->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     */
    public function mobileRelatedAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function mobileRelatedgridAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     */
    public function mobileAddonsrelatedAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_addons')
            ->setProductsRelated($this->getRequest()->getPost('addons_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function mobileAddonsrelatedgridAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_addons')
            ->setProductsRelated($this->getRequest()->getPost('addons_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     */
    public function mobileConfigrelatedAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_configs')
            ->setProductsRelated($this->getRequest()->getPost('configs_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function mobileConfigrelatedgridAction()
    {
        $this->_initMobileSubscription();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_mobile_edit_tab_configs')
            ->setProductsRelated($this->getRequest()->getPost('configs_related', null));
        $this->renderLayout();
    }

    /**
     * Initialize Subscription from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initMobileSubscription()
    {
        $this->_title($this->__('Subscription'))
             ->_title($this->__('Manage Content'));

        $subscriptionId = $this->getRequest()->getParam('subscription_id');
        $subscription = Mage::getModel('tele2_subscription/mobile');

        if ($subscriptionId) {
            $subscription->load($subscriptionId);

            if (! $subscription->getSubscriptionId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('This page no longer exists.'));
                $this->_redirect('*/*/mobile');
                return;
            }
        }

        $this->_title($subscription->getSubscriptionId() ? $subscription->getName() : $this->__('New Subscription'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $subscription->setData($data);
        }

        Mage::register('subscription', $subscription);
        Mage::register('current_subscription', $subscription);

        return $subscription;
    }

    public function mobileSaveAction()
    {
        if ($data = $this->getRequest()->getPost('data')) {

            //init subscription model and set data
            $subscription = Mage::getModel('tele2_subscription/mobile');

            $updateData = array();
            if (isset($data['subscription_id']) && $subscriptionId = $data['subscription_id']) {
                $subscription->load($subscriptionId);
                $subscriptionOldData = $subscription->getData();
                $updateData['subscription_id'] = $subscription->getSubscriptionId();
                $updateData['entity_id'] = $subscription->getId();
            } else {
                $subscriptionOldData = array();
            }
            foreach ($data as $field => $value) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            $subscriptionHelper = Mage::helper("tele2_subscription");
            // Save an image
            $image = $subscriptionHelper->uploadImage('image', 'catalog/subscription');
            if (!is_null($image)) {
                $updateData['image'] = $image;
            }

            $subscription->setData($updateData);

            // try to save it
            try {
                // save the data
                $subscription->save();

                /** Save Binding periods data */
                $subscription->saveBinding($this->getRequest());
                if ($this->getRequest()->getParam('remove_binding')) {
                    $subscriptionOldData['update_bindings'] = true;
                }

                /** Save Associated Add-ons */
                Mage::getModel('tele2_subscription/addonRelation')
                    ->saveAddons($subscription, $this->getRequest());

                /** Save Associated Subsciption Config */
                Mage::getModel('tele2_subscription/configRelation')
                    ->saveConfigs($subscription, $this->getRequest());

                /** Save Associated Products */
                $subscription->saveAssocProducts($this->getRequest(), $updateData, $subscriptionOldData);

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('Subscription has been saved.')
                );
                // clear previously saved data from session

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/mobileEdit', array('subscription_id' => $subscription->getSubscriptionId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
            catch (Exception $e) {
                Mage::logException($e);
                    $this->_getSession()->addException($e,
                    Mage::helper('tele2_subscription')->__('An error occurred while saving the subscription.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/mobileEdit', array('subscription_id' => $this->getRequest()->getParam('subscription_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $subscriptionId = $this->getRequest()->getParam('subscription_id');
        if ($subscriptionId) {
            try {
                $subscription = Mage::getModel('tele2_subscription/mobile')
                    ->load($subscriptionId)
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('Subscription has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('Subscription has not been deleted.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
    }

    public function associatedGridAction()
    {
        $subscriptionId = $this->getRequest()->getParam('subscription_id');
        $subscr = Mage::getModel('tele2_subscription/mobile')->load($subscriptionId);

        Mage::register('subscription', $subscr);
        $this->getResponse()->setBody(
            $this->getLayout()
            ->createBlock('tele2_subscription/adminhtml_mobile_edit_tab_products_grid')
            ->toHtml()
        );

    }

    public function associatedAddonsGridAction()
    {
        $subscriptionId = $this->getRequest()->getParam('subscription_id');
        $subscr = Mage::getModel('tele2_subscription/mobile')->load($subscriptionId);

        Mage::register('subscription', $subscr);
        $this->getResponse()->setBody(
            $this->getLayout()
            ->createBlock('tele2_subscription/adminhtml_mobile_edit_tab_addons_grid')
            ->toHtml()
        );

    }

    public function associatedconfiggridAction()
    {
        $subscriptionId = $this->getRequest()->getParam('subscription_id');
        $subscr = Mage::getModel('tele2_subscription/mobile')->load($subscriptionId);

        Mage::register('subscription', $subscr);
        $this->getResponse()->setBody(
            $this->getLayout()
            ->createBlock('tele2_subscription/adminhtml_mobile_edit_tab_config_grid')
            ->toHtml()
        );
    }

    /**
     * Subscription Config Action
     */
    public function configAction()
    {
        $this->_title($this->__('Tele 2'))
          ->_title($this->__('Subscription Configs'));

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Subscription grid for AJAX request
     */
    public function configGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function configNewAction()
    {
        $this->_forward('configEdit');
    }
    
    public function configEditAction()
    {
        $this->_title($this->__('Subscription Config'))
             ->_title($this->__('Manage Content'));

        
        $subscriptionConfigId = $this->getRequest()->getParam('config_id');
        $subscriptionConfig = Mage::getModel('tele2_subscription/config');

        if ($subscriptionConfigId) {
            $subscriptionConfig->load($subscriptionConfigId);

            if (! $subscriptionConfig->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($subscriptionConfig->getId() ? $subscriptionConfig->getName() : $this->__('New Page'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $subscriptionConfig->setData($data);
        }

        Mage::register('subscription_config', $subscriptionConfig);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function configSaveAction()
    {
        if ($data = $this->getRequest()->getPost('data')) {
  
            //init subscription model and set data
            $subscriptionConfig = Mage::getModel('tele2_subscription/config');
  
            $updateData = array();
            foreach ($data as $field => $value) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }
            
            $subscriptionHelper = Mage::helper("tele2_subscription");
            // Save image
            $imageMain = $subscriptionHelper->uploadImage('image_main');
            if (!is_null($imageMain)) {
                $updateData['image_main'] = $imageMain;
            }

            $subscriptionConfig->setData($updateData);

            // try to save it
            try {
                // save the data
                $subscriptionConfig->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('Subscription Config Entity has been saved.')
                );
                // clear previously saved data from session

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/configEdit', array('config_id' => $subscriptionConfig->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/config');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('tele2_subscription')->__('An error occurred while saving the subscription config entity.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/configEdit', array('config_id' => $this->getRequest()->getParam('config_id')));
            return;
        }
        $this->_redirect('*/*/config');
    }

    public function configDeleteAction()
    {
        $subscriptionConfigId = $this->getRequest()->getParam('config_id');
        if ($subscriptionConfigId) {
            try {
                $config = Mage::getModel('tele2_subscription/config')
                    ->load($subscriptionConfigId)
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tele2_subscription')->__('Subscription Config Entity has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tele2_subscription')->__('Subscription Config Entity has not been deleted.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/config');
    }
}

