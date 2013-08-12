<?php
class saveAdditionalInfoTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Mage::app('default');
        Mage::app()->getRequest()->setParam('expectedDeliveryTime', '1-2 dagar');
        $this->_product = $this->_getProductMock();
        $this->_quoteItem = Mage::getModel('sales/quote_item');
        $this->_modelOffer = Mage::getModel('tele4G_checkout/offer');
    }
    
    protected function _getProductMock()
    {
        $product = Mage::getModel('catalog/product');
        $product->setArticleid('123Article');
        $product->setPartnerid('123Partner');
        $product->setMake('123Make');
        $product->setDescription('123Description');
        $product->setPricewithoutvat('111');
        $product->setPricewithvat('100');
        return $product;
    }

    public function testSetProductDataToQuoteItem()
    {
        $this->_modelOffer->setProductDataToQuoteItem($this->_quoteItem, $this->_product);
        $this->assertEquals($this->_quoteItem->getArticleId(), $this->_getProductMock()->getArticleid());
        $this->assertEquals($this->_quoteItem->getPartnerId(), $this->_getProductMock()->getPartnerid());
        $this->assertEquals($this->_quoteItem->getMake(), $this->_getProductMock()->getMake());
        $this->assertEquals($this->_quoteItem->getDescription(), $this->_getProductMock()->getDescription());
        $this->assertEquals($this->_quoteItem->getExpectedDeliveryTime(), Mage::app()->getRequest()->getParam('expectedDeliveryTime'));
        $additionalData = unserialize($this->_quoteItem->getAdditionalData());
        $this->assertEquals($additionalData['price_without_vat'], $this->_getProductMock()->getPricewithoutvat());
        $this->assertEquals($additionalData['price_with_vat'], $this->_getProductMock()->getPricewithvat());
    }
    
    public function testSaveAdditionalInfoForServices()
    {
        $this->_modelOffer->saveAdditionalInfoForServices($this->_product, $this->_quoteItem);
        $this->assertEquals($this->_quoteItem->getArticleId(), $this->_getProductMock()->getArticleid());
        $this->assertEquals($this->_quoteItem->getPartnerId(), $this->_getProductMock()->getPartnerid());
        $this->assertEquals($this->_quoteItem->getMake(), $this->_getProductMock()->getMake());
        $this->assertEquals($this->_quoteItem->getDescription(), $this->_getProductMock()->getDescription());
        $additionalData = unserialize($this->_quoteItem->getAdditionalData());
        $this->assertEquals($additionalData['price_without_vat'], $this->_getProductMock()->getPricewithoutvat());
        $this->assertEquals($additionalData['price_with_vat'], $this->_getProductMock()->getPricewithvat());
    }
   
}
