<?php
/**
 * Configuration controller
 *
 * @category   Tele2
 * @package    Tele2_Adminhtml
 */
require ROOT_PATH.'app/code/core/Mage/Adminhtml/controllers/System/ConfigController.php';
class Tele2_Adminhtml_System_ConfigController extends Mage_Adminhtml_System_ConfigController
{

    /**
     * Edit configuration section
     * 
     * Rewrited, because there are no event between create block in Layout and render Layout
     *
     */
    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        Mage::getSingleton('adminhtml/config_data')
            ->setSection($current)
            ->setWebsite($website)
            ->setStore($store);

        $configFields = Mage::getSingleton('adminhtml/config');

        $sections     = $configFields->getSections($current);
        $section      = $sections->$current;
        $hasChildren  = $configFields->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            $this->_redirect('*/*/', array('website'=>$website, 'store'=>$store));
        }

        $this->loadLayout();

        $this->_setActiveMenu('system/config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'),
            $this->getUrl('*/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());

        if ($this->_isSectionAllowedFlag) {
            $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/websiterestrictions.phtml'));

            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/shipping/ups.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/shipping/applicable_country.phtml'));

            $this->renderLayout();
        }
    }
}
