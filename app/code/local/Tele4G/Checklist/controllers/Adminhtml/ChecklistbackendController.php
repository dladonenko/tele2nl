<?php
class Tele4G_Checklist_Adminhtml_ChecklistbackendController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
           $this->loadLayout();
	   $this->_title($this->__("Checklist"));
	   $this->renderLayout();
    }
}