<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Block_Adminhtml_Catalog_Product_Options extends Mage_Adminhtml_Block_Widget
{
    protected $_itemCount = 1;

    protected $_values;
    
    protected $_subscriptions = array();
    protected $_bindings = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('subscription/catalog/product/options.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Subscription'),
                    'class' => 'delete delete-product-option '
                ))
        );
        return parent::_prepareLayout();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrive Product's Custom options as Subscriptions
     *
     * @return array
     */
    public function getOptionValues()
    {
        $options = $this->getProduct()->getOptions();

        $values = array();
        foreach($options as $option) {
            foreach ($option->getValues() as $_value) {

                $sku = $_value->getSku();
                if (preg_match("%subscr-(\d+)-(\d+)%", $sku, $result)) {
                    
                    $subscription = Mage::getModel('catalog/product')->load($result[1]);
                    
                    $this->setItemCount($_value->getOptionTypeId());
                    $value['id'] = $_value->getOptionTypeId();
                    $value['item_count'] = $this->getItemCount();
                    $value['subscription_id'] = $result[1];
                    $value['bind_period'] = $result[2];
                    $origin_price = ((double)$subscription->getData('subsidy_price') * $value['bind_period']);
                    $value['discount'] = (abs((double)$_value->getPrice()) - $origin_price < 1 ? 0 : abs((double)$_value->getPrice()) - $origin_price);
                    $value['option_id'] = $_value->getOptionTypeId();

                    $values[] = new Varien_Object($value);
                }
            }
        }
        $this->_values = $values;
        return $this->_values;
    }

    public function getItemCount()
    {
        return $this->_itemCount;
    }

    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }

    /**
     * Retrive Subscriptions Collection
     *
     * @return Tele2_Subscription_Mobile_Collection
     */
    public function getSubscriptions()
    {
        if (!$this->_subscriptions) {
            $this->_subscriptions = Mage::getResourceModel('tele2_subscription/mobile_collection');
        }
        return $this->_subscriptions;
    }

    /**
     * Retrieve Binding Periods
     *
     * @return array
     */
    public function getAllBindPeriods()
    {
        if (!$this->_bindings) {
            $_subscriptions = $this->getSubscriptions();
            foreach ($_subscriptions as $_subscription) {
                $this->_bindings[$_subscription->getSubscriptionId()] = $_subscription->getBindings();
            }
        }
        return $this->_bindings;
    }

    public function getBindPeriod()
    {
        $aBindPeriod = array(
            array('label'=>12),
            array('label'=>18),
            array('label'=>24),
            array('label'=>36)
        );
        return $aBindPeriod;
    }
}
