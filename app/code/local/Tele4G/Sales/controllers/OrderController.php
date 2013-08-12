<?php
/**
 * Hack to avoid authorization requirements in view order
 */
require ROOT_PATH.'app/code/core/Mage/Sales/controllers/OrderController.php';
class Tele4G_Sales_OrderController extends Mage_Sales_OrderController
{

    protected function _canViewOrder($order)
    {
        return true;
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        if ($action != 'print') {
            parent::preDispatch();
            $loginUrl = Mage::helper('customer')->getLoginUrl();

            if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            }
        }
    }
}