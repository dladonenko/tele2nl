<?php
class Tele4G_Common_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (Mage::helper('tele4G_checkout')->isAssistant()) {
            $body = json_encode(array('action' => "validatenumber();"));
        } else {
            $body = json_encode(array('action' => "sendsms();"));
        }
        return $this->getResponse()->setBody($body);
    }
}
