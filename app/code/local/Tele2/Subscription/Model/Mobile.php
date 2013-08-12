<?php
/**
 * Magento Enterprise Edition
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Model_Mobile extends Tele2_Subscription_Model_Subscription
{
    /**
     * Subscription type
     *
     * @var string
     */
    protected $_subscriptionType = 'mobile';

    const SUBSCRIPTION_TYPE1_PRE  = 1;
    const SUBSCRIPTION_TYPE1_POST = 2;
    const SUBSCRIPTION_TYPE1_BB_PRE  = 3;
    const SUBSCRIPTION_TYPE1_BB_POST = 4;

    const SUBSCRIPTION_TYPE1_PRE_TEXT  = 'Mobile Voice Prepaid';
    const SUBSCRIPTION_TYPE1_POST_TEXT = 'Mobile Voice Postpaid';
    const SUBSCRIPTION_TYPE1_BB_PRE_TEXT  = 'Mobile Broadband Prepaid';
    const SUBSCRIPTION_TYPE1_BB_POST_TEXT = 'Mobile Broadband Postpaid';

    const SUBSCRIPTION_TYPE2_S       = 1;
    const SUBSCRIPTION_TYPE2_M       = 2;
    const SUBSCRIPTION_TYPE2_L       = 3;
    const SUBSCRIPTION_TYPE2_SIMONLY = 4;

    const SUBSCRIPTION_TYPE2_S_TEXT       = 'Small';
    const SUBSCRIPTION_TYPE2_M_TEXT       = 'Medium';
    const SUBSCRIPTION_TYPE2_L_TEXT       = 'Large';
    const SUBSCRIPTION_TYPE2_SIMONLY_TEXT = 'Sim Only';

    const SUBSCRIPTION_DOWNGRADE_A   = 1;
    const SUBSCRIPTION_DOWNGRADE_GN0 = 2;
    const SUBSCRIPTION_DOWNGRADE_GN1 = 3;
    const SUBSCRIPTION_DOWNGRADE_GN2 = 4;

    const SUBSCRIPTION_DOWNGRADE_A_TEXT   = 'NA';
    const SUBSCRIPTION_DOWNGRADE_GN0_TEXT = 'GN0';
    const SUBSCRIPTION_DOWNGRADE_GN1_TEXT = 'GN1';
    const SUBSCRIPTION_DOWNGRADE_GN2_TEXT = 'GN2';

    const SUBSCRIPTION_SIMTYPE_1 = 1;
    const SUBSCRIPTION_SIMTYPE_2 = 2;
    const SUBSCRIPTION_SIMTYPE_3 = 3;
    const SUBSCRIPTION_SIMTYPE_4 = 4;

    const SUBSCRIPTION_TYPE_POST = 'post';
    const SUBSCRIPTION_TYPE_PRE  = 'pre';

    /**
     * Constructor
     *
     * @param array $data
     * @param Mage_Core_Model_Config $config
     * @param Tele2_Subscription_Helper_Data $helper
     */
    public function __construct($data = array(), $config = null, $helper = null)
    {
        parent::__construct($data, $config, $helper);
        $this->_init('tele2_subscription/mobile');
    }

    static public function getType1Options()
    {
        $_helper = Mage::helper('tele2_subscription');
        return array(
            self::SUBSCRIPTION_TYPE1_PRE => array(
                'value' => self::SUBSCRIPTION_TYPE1_PRE,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE1_PRE_TEXT)
            ),
            self::SUBSCRIPTION_TYPE1_POST => array(
                'value' => self::SUBSCRIPTION_TYPE1_POST,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE1_POST_TEXT)
            ),
            self::SUBSCRIPTION_TYPE1_BB_PRE => array(
                'value' => self::SUBSCRIPTION_TYPE1_BB_PRE,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE1_BB_PRE_TEXT)
            ),
            self::SUBSCRIPTION_TYPE1_BB_POST => array(
                'value' => self::SUBSCRIPTION_TYPE1_BB_POST,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE1_BB_POST_TEXT)
            ),
        );
    }

    static public function getType2Options()
    {
        $_helper = Mage::helper('tele2_subscription');
        return array(
            self::SUBSCRIPTION_TYPE2_S => array(
                'value' => self::SUBSCRIPTION_TYPE2_S,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE2_S_TEXT)
            ),
            self::SUBSCRIPTION_TYPE2_M => array(
                'value' => self::SUBSCRIPTION_TYPE2_M,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE2_M_TEXT)
            ),
            self::SUBSCRIPTION_TYPE2_L => array(
                'value' => self::SUBSCRIPTION_TYPE2_L,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE2_L_TEXT)
            ),
            self::SUBSCRIPTION_TYPE2_SIMONLY => array(
                'value' => self::SUBSCRIPTION_TYPE2_SIMONLY,
                'label' => $_helper->__(self::SUBSCRIPTION_TYPE2_SIMONLY_TEXT)
            )
        );
    }

    static public function getDowngradeOptions()
    {
        $_helper = Mage::helper('tele2_subscription');
        return array(
            self::SUBSCRIPTION_DOWNGRADE_A => array(
                'value' => self::SUBSCRIPTION_DOWNGRADE_A,
                'label' => $_helper->__(self::SUBSCRIPTION_DOWNGRADE_A_TEXT)
            ),
            self::SUBSCRIPTION_DOWNGRADE_GN0 => array(
                'value' => self::SUBSCRIPTION_DOWNGRADE_GN0,
                'label' => $_helper->__(self::SUBSCRIPTION_DOWNGRADE_GN0_TEXT)
            ),
            self::SUBSCRIPTION_DOWNGRADE_GN1 => array(
                'value' => self::SUBSCRIPTION_DOWNGRADE_GN1,
                'label' => $_helper->__(self::SUBSCRIPTION_DOWNGRADE_GN1_TEXT)
            ),
            self::SUBSCRIPTION_DOWNGRADE_GN2 => array(
                'value' => self::SUBSCRIPTION_DOWNGRADE_GN2,
                'label' => $_helper->__(self::SUBSCRIPTION_DOWNGRADE_GN2_TEXT)
            )
        );
    }

    public function getAttributeText($value = false)
    {
        if (!$value) {
            return '';
        }
        switch ($value)
        {
            case 'type1':
                $type1Options = $this->getType1Options();
                return $type1Options[$this->getData($value)]['label'];
                break;
        }
        return '';
    }

    /**
     * Generate Product's custom options as Subscriptions
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function generateOption($product)
    {
        try {
            $option = $this->_config->getModelInstance('catalog/product_option');

            $values = array();
            foreach ($this->_getBindingTimes() as $bindingTime) {
                $values[] = $this->_getValueArray($bindingTime); 
            }

            $option->setProductId($product->getId())
                ->setType('drop_down')
                ->setTitle('subscriptions')
                ->setValues($values)
                ->save();

            $this->_saveRelation($option, $product);

            if (!$product->getHasOptions())
                $product->setHasOptions(true)
                  ->save();
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * Modify Product's custom options as Subscriptions and save
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @param Mage_Catalog_Model_Product $product
     * @param int $subscriptionId
     */
    public function modifyOption($option, $product, $subscriptionId)
    {
        try {
            $oldSubscriptionData = $this->_subscriptionOldData;
            $oldSubsidyPrice = $oldSubscriptionData['subsidy_price'];

            $newValues = array();
            $found     = false;
            foreach ($option->getValues() as $value) {
                $sku = $value->getSku();
                if (preg_match("%subscr-" . $this->getSubscriptionId() . "-(\d+)%", $sku, $m)) {//old options
                    $bindingPeriod = $m[1];
                    if (!$this->getBindingByPeriod($bindingPeriod)) {//if this bp was deleted, do not include it in new option
                        continue;
                    }
                    $oldDefaultOptionPrice = $oldSubsidyPrice*$bindingPeriod;
                    $optionPrice = $value->getPrice();
                    if ($oldDefaultOptionPrice == abs($optionPrice)) {
                        //option was not changed by admin locally, and shlould be be changed now by new subsidy price
                        $newValues[] = $this->_getValueArray($bindingPeriod);
                    } else {
                        $newValues[] = $this->_getValueArray($bindingPeriod, $optionPrice);
                    }

                    $found = true;
                } else {
                    $newValues[] = array(
                        'sku'   => $value->getSku(),
                        'price' => $value->getPrice(),
                        'title' => $value->getTitle(),
                        'price_type' => 'fixed',
                    );
                }
            }
            if (!$found) {
                foreach ($this->_getBindingTimes() as $bindingPeriod) {
                    $newValues[] = $this->_getValueArray($bindingPeriod);
                }
            }

            $option->delete();
            $option = $this->_config->getModelInstance('catalog/product_option')
                ->setProductId($product->getId())
                ->setType('drop_down')
                ->setTitle('subscriptions')
                ->setValues($newValues)
                ->save();
            
            $this->_saveRelation($option, $product);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * Get All Subscriptions
     *
     * @return null|Tele2_Subscription_Model_Resource_Mobile_Collection
     */
    public function getAllSubscriptions()
    {
        if (is_null($this->_subscriptionCollection)) {
            $this->_subscriptionCollection = $this->_config->getResourceModelInstance('tele2_subscription/mobile_collection');
        }
        return $this->_subscriptionCollection;
    }

    /**
     * Retrive Subscription by related fake subscription product 
     * 
     * @param int $productId
     * @return Tele2_Subscription_Model_Subscription
     */
    public function getSubscriptionByProductId($productId)
    {
        return $this->loadByFakeProduct($productId);
//        return $this->getAllSubscriptions()
//            ->addFieldToFilter('fake_product_id', $productId)
//            ->getFirstItem();
    }

    /**
     * Get Value Array
     *
     * @param int $bindingTime
     * @param null|float $oldOptionPrice
     * @todo: fix inconsistency: child protected function _getValueArray has other signature - ($subscription)
     * @return array
     */
    protected function _getValueArray($bindingTime, $oldOptionPrice = null)
    {
        $toChangePrice = $this->_toChangeSubscriptionPrice;
        if ($oldOptionPrice && !$toChangePrice) {
            $newOptionPrice = $oldOptionPrice;
        } else {
            $newOptionPrice = $bindingTime * $this->getSubsidyPrice() * (-1);
        }

        return array(
                'sku'    => 'subscr-' . $this->getSubscriptionId() . '-' . $bindingTime,
                'price'  => $newOptionPrice,
                'title'  => 'subscr-' . $this->getSubscriptionId() . '-bind-' . $bindingTime,
                'price_type' => 'fixed'
        );
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        $bind_period = $this->getParamBindPeriod();
        foreach ($this->getBindings() as $binding) {
            if ($binding->getTime() == $bind_period && $binding->getMonthlyPriceWithVat() > 0) {
                return $binding->getMonthlyPriceWithVat();
            }
        }
        return $this->getData('price');
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $subscriptionAttributes = Mage::getModel('tele2_subscription/MobileAttributes');
        $subscriptionAttributesId = null;
        if ($this->getSubscriptionId()) {
            $subscriptionAttributes->load($this->getSubscriptionId(), 'subscription_id');
            $subscriptionAttributesId = $subscriptionAttributes->getId();
        }
        $subscriptionAttributes->setData($this->getData());
        $subscriptionAttributes->setId($subscriptionAttributesId);
        $subscriptionAttributes->save();
        $this->_skipAttributesSaving = true;
        return parent::_afterSave();
    }
}
