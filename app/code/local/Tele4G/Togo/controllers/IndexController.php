<?php
class Tele4G_Togo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function getResellersTogoAction()
    {
        $city = $this->getRequest()->getPost('city', '');
        $block = $this->getLayout()
            ->createBlock('tele4G_catalog/product_view')
            ->setTemplate('catalog/product/resellerstogo.phtml')
            ->setData("city", $city)
            ->toHtml();
        $this->getResponse()->setBody($block);
    }
}