<?php
/**
 * One page checkout success page
 *
 * @category   Tele4G
 * @package    Tele4G_Checkout
 * @author     Ciklum
 */
class Tele4G_Checkout_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function getOrder()
    {
        $order = Mage::getModel('tele4G_sales/order');
        $order->loadByIncrementId($this->getOrderId());
        return $order;
    }

    public function getOrderedItems()
    {
        $order = $this->getOrder();

        $itemsCollection = $order->getItemsCollection();

        $items = array();
        foreach ($itemsCollection as $item) {
            if (!$item->getParentItemId()) {
                $items[] = $item;/*array(
                    'name'=>$item->getName(),
                    //'url_key'=>$item->getUrlKey(),
                    'price'=>$item->getPrice(),
                    'image'=>Mage::helper('catalog/image')->init($item->getProduct(), 'small_image'),
                )*/;
            } else {
                $optionProducts[$item->getParentItemId()] = $item;
            }
        }

        return $items;
    }

    public function getOrderSS4()
    {
        $order = $this->getOrder();
        $offerData = $order->getOfferData();
        if ($offerData) {
            $offerData = unserialize($offerData);
        }
        if (isset($offerData['ss4_order']['ss4_order_id'])) {
            return $offerData['ss4_order']['ss4_order_id'];
        } else {
            return false;
        }
    }
}
