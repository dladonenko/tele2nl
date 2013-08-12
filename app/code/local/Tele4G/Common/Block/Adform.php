<?php
class Tele4G_Common_Block_Adform extends Mage_Core_Block_Template
{
    const ADFORM_TRACKING_CAMPAIGN_ID = '25104';
    const ADFORM_TRACKING_POINT_ID_ADDON = '1069871';
    const ADFORM_TRACKING_POINT_ID_KASSA = '1069872';
    const ADFORM_TRACKING_POINT_ID_KVITTO = '1069873';
    const ADFORM_SOLUTION_ENABLED = 'adform_track/settings/oldsolution_enabled';

    public $trackingPoint;

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _canShowTracking()
    {
        $isActive = Mage::getStoreConfigFlag(self::ADFORM_SOLUTION_ENABLED);
        if (!$isActive) {
            return false;
        }

        $trackingPoint = $this->getTrackingPoint();
        if ($trackingPoint) {
            $this->trackingPoint = $trackingPoint;
            return true;
        }

        return false;
    }

    protected function _toHtml()
    {
        if (!$this->_canShowTracking()) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getTrackingPoint()
    {
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $module = $this->getRequest()->getModuleName();

        if ($module=='checkout' && $controller=='cart' && $action=='index') {//product has been added to cart
            return self::ADFORM_TRACKING_POINT_ID_ADDON;
        }

        if ($module=='checkout' && $controller=='tele4G' && $action=='index') {//customet reached checkout page
            return self::ADFORM_TRACKING_POINT_ID_KASSA;
        }

        if ($module=='checkout' && $controller=='onepage' && $action=='success') {//receipt page
            return self::ADFORM_TRACKING_POINT_ID_KVITTO;
        }

        return null;
    }

    public function getTrackingIds()
    {
        if ($this->trackingPoint) {
            return 'adf.track(' . self::ADFORM_TRACKING_CAMPAIGN_ID . ', ' . $this->trackingPoint . ');';
        }
        return '';
    }

    public function getTrackingImg()
    {
        if ($this->trackingPoint) {
            $image = "<img src='http://track.adform.net/Serving/TrackPoint/?pm="
                . self::ADFORM_TRACKING_CAMPAIGN_ID . "&amp;lid="
                . $this->trackingPoint . "' width='1' height='1' alt='' />";
            return $image;
        }
        return '';
    }

    public function getOrderTracking()
    {
        if ($this->trackingPoint == self::ADFORM_TRACKING_POINT_ID_KVITTO) {
            if ($order = $this->_getLastOrder()) {
                $orderTracking = Mage::helper('tele4G_common')->getTracking($order);
                $result = $orderTracking->getCreateOrder() . $orderTracking->getAddProducts();

                if ($result) {
                    return $result;
                }
            }
        }
        return '';
    }
    
    protected function _getLastOrder()
    {
        $lastId =  Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastId);
        return $order;
    }
}
