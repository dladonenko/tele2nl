<?php
/**
 * Tele4G Subscriptions All Products controller
 *
 * @category   Tele4G
 * @package    Tele4G_Subscription
 */
class Tele4G_Subscription_AllproductsController extends Mage_Core_Controller_Front_Action
{
    protected function _initAction()
    {
        $this->loadLayout();
        return $this;
    }
    /**
     * Category view action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function downgradeAction()
    {
        $this->_initAction()
        ->renderLayout();
    }
}