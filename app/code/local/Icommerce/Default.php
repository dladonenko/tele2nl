<?php 

class Icommerce_Default { 
	
	
    static function logAppend( $msg, $file="var/icommerce.log" ){
        if( $file[0]!=="/" ){
            $file = Mage::getBaseDir()."/".$file;
        }
        return Icommerce_Log::append( $file, $msg );
    }
    
    
    static $_quote;
    static function getCheckoutQuote( ){
		if( !self::$_quote ){
			// Adapted to Magento 1.4.1
            $checkout = Mage::getSingleton('checkout/session');
			$quote_id = $checkout->getQuoteId();
			$quote = Mage::getModel('sales/quote')
					->setStoreId(Mage::app()->getStore()->getId());
			if( $quote_id ){
				$quote->load( $quote_id );
			}
			self::$_quote = $quote;
		}
        return self::$_quote;
    }

}
