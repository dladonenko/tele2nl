<?php
/**
 * Magento Enterprise Edition
 *
 * @category   Tele4G
 * @package    Tele4G_Sales
 */

/**
 * Tele4G Sales observer
 *
 */
class Tele4G_Sales_Model_Observer
{
    public function salesOrderAfterPlace($observer)
    #public function salesOrderAfterPlace()
    {
        try {
            #$orderId = 106;
            #$order = Mage::getModel('sales/order')->load($orderId);
            $order = Mage::getModel('sales/order')->load($observer->getOrder()->getId());
            if (
                $order->getPayment()->getMethod() != 'tele4G_auriga' &&
                $order->getPayment()->getMethod() != 'tele4G_dibs'
            ) {
                $response = Mage::getModel('tele4G_sS4Integration/sS4Integration')
                    ->creatOrder($order);
                    self::saveOrderResponse($order, $response);
               $this->addFriendPhoneToComment($order);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Tell a friend campaign.
     * Add Friend's Phone number as comment to order.
     * 
     * @param Mage_Sales_Model_Order $order
     * @return \Tele4G_Sales_Model_Observer
     */
    public function addFriendPhoneToComment($order)
    {
        $offerData = unserialize($order->getOfferData());
        if (isset($offerData['friend_phone']) && $friendPhone = $offerData['friend_phone']) {
            $order->addStatusHistoryComment($friendPhone, false);
            $order->save();
        }
        return $this;
    }

    public static function saveOrderResponse($order, $response)
    {
        $offerData = $order->getOfferData();
        if ($offerData) {
            $offerData = unserialize($offerData);
        } else {
            $offerData = array();
        }
        
        if($response){
            $xmlResponse = simplexml_load_string($response);
            $nodes = $xmlResponse->xpath('/soap:Envelope/soap:Body/*');
            $nodes = array_shift($nodes);
            if (
                isset($nodes->createOrderResponse->responseStatus->errorCode) &&
                isset($nodes->createOrderResponse->responseStatus->status)
            ) {
                $result = array(
                    'ss4_order' => array(
                        (string)$nodes->createOrderResponse->responseStatus->errorCode =>
                        (string)$nodes->createOrderResponse->responseStatus->errorName,
                        'status'       => (string)$nodes->createOrderResponse->responseStatus->status,
                        'errorName'       => (string)$nodes->createOrderResponse->responseStatus->errorName,
                        'ss4_order_id' => '',
                    )
                );
            } elseif (
                isset($nodes->faultcode) &&
                isset($nodes->faultstring)
            ) {
                $result = array(
                    'ss4_order' => array(
                        'error_code'   => (string)$nodes->faultcode,
                        'status'       => (string)$nodes->faultstring,
                        'ss4_order_id' => '',
                    )
                );
            } else {
                $result = array(
                'ss4_order' => array(
                    'error_code'   => '',
                    'status'       => 'Unkmown error.',
                    'ss4_order_id' => 'qwe',
                )
                );
            }
            if (isset($nodes->createOrderResponse->orderId)){
                $result['ss4_order']['ss4_order_id'] = (string)$nodes->createOrderResponse->orderId;
                $order->setIncrementId((string)$nodes->createOrderResponse->orderId);
            }
        } else {
            $result = array(
                'ss4_order' => array(
                    'error_code'   => '',
                    'status'       => 'empty response',
                    'errorName'    => 'bad connection', 
                    'ss4_order_id' => 'qwe',
                )
                );
        }
        //$offerData = array_merge($offerData, $result);
        $offerData = $offerData + $result;
        if (isset($result['ss4_order']['errorName']) && $result['ss4_order']['errorName'] != "") {
            $error_name = $result['ss4_order']['errorName'];
            Mage::getSingleton('checkout/session')->setSs4Error($error_name);
        }

        /** @todo potential error based on order save event */
        $order
            ->setOfferData(serialize($offerData))
            ->save();
    }

}
