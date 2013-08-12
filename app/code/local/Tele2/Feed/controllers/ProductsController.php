<?php

class Tele2_Feed_ProductsController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        return $this->deviceconfAction();
    }
    
    public function deviceconfAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}