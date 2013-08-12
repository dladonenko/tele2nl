<?php
/**
 * Our shipping method module adapter
 */
class Tele4G_Togo_Model_Carrier_Togo extends Mage_Shipping_Model_Carrier_Abstract
{
    /**
     * unique internal shipping method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'togo';
    //protected $_isFixed = true;

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        // skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier('togo');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('togo');
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice('0.00');
        $method->setCost('0.00');

        $result->append($method);

        return $result;
    }

    /**
     * This method is used when viewing / listing Shipping Methods with Codes programmatically
     */
    public function getAllowedMethods() {
        return array($this->_code => $this->getConfigData('name'));
    }
}