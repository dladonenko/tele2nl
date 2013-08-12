<?php
    class Tele4G_Sales_Block_Adminhtml_Sales_Order_Grid_Renderer_FriendPhone extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            $value =  $row->getData($this->getColumn()->getIndex());
            if ($value) {
                $offerData = unserialize($value);
                if (isset($offerData['friend_phone']) && !empty($offerData['friend_phone'])) {
                    return $offerData['friend_phone'];
                }
            }
            return null;
        }
    }
