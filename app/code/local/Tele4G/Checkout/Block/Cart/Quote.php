<?php

class Tele4G_Checkout_Block_Cart_Quote extends Mage_Checkout_Block_Cart_Abstract 
{
    /**
     * Get active or custom quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote();
        }

        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
    
    public function getMonthlyPrice()
    {
        return $this->getQuote()->getMonthlyPriceAmount();
    }
    
    public function getLeastTotalCost()
    {
        return $this->getQuote()->getLeastTotalCost();
    }
}

?>
