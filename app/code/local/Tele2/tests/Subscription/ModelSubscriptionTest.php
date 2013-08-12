<?php
class Tele2_Subscription_Model_SubscriptionTest extends PHPUnit_Framework_TestCase {

    public function testGetRelatedProductsIds()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(array('subscription_id' => 11), $this->_getMockedConfigForRelatedProductsIds());
        $this->assertEquals($subscriptionModel->getRelatedProductsIds(), array(1,2,3));
    }

    public function testGetRelatedProducts()
    {
        $subscriptionModel = $this->getMock(
            'Tele2_Subscription_Model_Subscription',
            array('getRelatedProductsIds'),
            array(array('subscription_id' => 11, 'store_id' => 1), $this->_getMockedConfigForRelatedProducts())
        );

        $subscriptionModel->expects($this->once())
            ->method('getRelatedProductsIds')
            ->will($this->returnValue(array(1,2,3)));

        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $subscriptionModel->getRelatedProducts());
    }

    public function testGetSubscriptionProductsCollection()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForSubscriptionProductsCollection()
        );

        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Collection',
            $subscriptionModel->getSubscriptionProductsCollection()
        );
    }

    public function testGetSubscriptionsByProduct()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForSubscriptionsByProduct()
        );

        $product = new Mage_Catalog_Model_Product();
        $product->setId(11);
        $this->assertTrue(count($subscriptionModel->getSubscriptionsByProduct($product)) == 1);

    }

    /**
     * @expectedException Exception
     */
    public function testGetSubscriptionsByProductGetException()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription();
        $product = new Varien_Object();
        $subscriptionModel->getSubscriptionsByProduct($product);
    }

    public function testGetBindings()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForBindings()
        );

        $this->assertTrue(count($subscriptionModel->getBindings()) == 1);
    }

    public function testGetBindingById()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForBindings()
        );
        $this->assertInstanceOf('Tele2_Subscription_Model_Binding', $subscriptionModel->getBindingById(1));
    }

    public function testGetBindingByPeriod()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForBindings()
        );
        $date = mktime(22, 0, 0, 2, 7,2013);
        $binding = $subscriptionModel->getBindingByPeriod($date);
        $this->assertEquals($binding->getTime(), $date);
    }

    public function testGetBindPrices()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForBindings()
        );
        $this->assertTrue(count($subscriptionModel->getBindPrices()) == 1);
    }


    public function testSaveBinding()
    {
        $bindingParams = array(
            'article_id' => 'articleId-123',
            'time' => mktime(20, 0, 0, 11, 22, 2012),
            'monthly_price_with_vat' => 120,
            'monthly_price_without_vat' => 100,
        );

        $removeBinding = array( 1 => 'on', 2 => 'on', 3 => 'on' );

        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForSaveBinding($bindingParams, $removeBinding)
        );

        $getParam = function ($modelClass) use ($bindingParams, $removeBinding)
        {
            $returnedData = array();
            switch ($modelClass) {
                case 'new_binding':
                    $returnedData = $bindingParams;
                    break;
                case 'remove_binding':
                    $returnedData = $removeBinding;
                    break;
            }
            return $returnedData;
        };

        $request  = $this->getMock(
            'Zend_Controller_Request_Abstract',
            array('getParam'),
            array(), '', false
        );

        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback($getParam));


        $subscriptionModel->saveBinding($request);
    }

    function testSaveAssocProducts()
    {
        $inProducts = array();
        $links = array(
            'related'
        );
        $allSelectedProducts = array('1');

        $helper = $this->getMock(
            'Tele2_Subscription_Helper_Data',
            array('unlinkProducts', 'generateCustomOptions'),
            array(), '', false
        );

        $helper->expects($this->any())
            ->method('unlinkProducts')
            ->with(array(1 => 4, 2 => 5), 11)
            ->will($this->returnValue());

        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForSaveAssocProducts(),
            $helper
        );

        $helper->expects($this->once())
            ->method('generateCustomOptions')
            ->with(1, $subscriptionModel);

        $getPost = function ($modelClass) use ($inProducts, $links, $allSelectedProducts)
        {
            $returnedData = array();
            switch ($modelClass) {

                case 'in_products':
                    $returnedData = $inProducts;
                    break;

                case 'links':
                    $returnedData = $links;
                    break;

                case 'selected_product':
                    $returnedData = $allSelectedProducts;
            }
            return $returnedData;
        };

        $request  = $this->getMock(
            'Zend_Controller_Request_Abstract',
            array('getPost'),
            array(), '', false
        );

        $request->expects($this->any())
            ->method('getPost')
            ->will($this->returnCallback($getPost));

        $updateData = array(
            'subscription_id' => 11,
            'subsidy_price' => 100,
            'price' => 110,
            'up_front_price' => 120,
            'update_bindings' => true,
        );

        $subscriptionModel->saveAssocProducts($request, $updateData);
    }

    public function testGetAddons()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForAddons()
        );
        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Resource_AddonRelation_Collection',
            $subscriptionModel->getAddons()
        );
    }

    public function testGetAddonCollection()
    {
        $subscriptionModel = $this->getMock(
            'Tele2_Subscription_Model_Subscription',
            array('getAddons'),
            array(array('subscription_id' => 11), $this->_getMockedConfigForAddonCollection())
        );

        $subscriptionModel->expects($this->once())
            ->method('getAddons')
            ->will($this->returnValue(array(
                new Varien_Object(array('addon_id' => 1)),
                new Varien_Object(array('addon_id' => 2)),
                new Varien_Object(array('addon_id' => 3))
            )));

        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Collection',
            $subscriptionModel->getAddonCollection()
        );
    }

    public function testGetConfigs()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11),
            $this->_getMockedConfigForConfigs()
        );
        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Resource_ConfigRelation_Collection',
            $subscriptionModel->getConfigs()
        );
    }

    public function testGetSubscriptionGroup()
    {
        $subscriptionModel = new Tele2_Subscription_Model_Subscription(
            array('subscription_id' => 11, 'subscription_group' => 1),
            $this->_getMockedConfigForSubscriptionGroup()
        );
        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Group',
            $subscriptionModel->getSubscriptionGroup()
        );
    }

    public function testLoad()
    {
        $subscriptionModel = $this->getMock(
            'Tele2_Subscription_Model_Subscription',
            array('_beforeLoad', '_getResource', '_afterLoad', 'setOrigData'),
            array(), '', false
        );

        $subscriptionModel->expects($this->once())
            ->method('_beforeLoad')
            ->with(11, 'subscription_id');

        $resource = $this->getMock(
            'Tele2_Subscription_Model_Resource_Subscription',
            array('load'),
            array(), '', false
        );

        $resource->expects($this->once())
            ->method('load')
            ->with($subscriptionModel, 11, 'subscription_id')
            ->will($this->returnValue($subscriptionModel));


        $subscriptionModel->expects($this->once())
            ->method('_getResource')
            ->will($this->returnValue($resource));

        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Subscription',
            $subscriptionModel->load(11)
        );
    }


    public function testLoadLoadByFakeProduct()
    {
        $subscriptionModel = $this->getMock(
            'Tele2_Subscription_Model_Subscription',
            array('loadByFakeProduct'),
            array(), '', false
        );

        $subscriptionModel->expects($this->once())
            ->method('loadByFakeProduct')
            ->with(11)
            ->will($this->returnValue($subscriptionModel));

        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Subscription',
            $subscriptionModel->load(11, 'fake_product_id')
        );
    }

    public function testLoadByFakeProduct()
    {
        $subscriptionModel = $this->getMock(
            'Tele2_Subscription_Model_Subscription',
            array('getResource'),
            array(), '', false
        );

        $resource = $this->getMock(
            'Tele2_Subscription_Model_Resource_Subscription',
            array('loadByFakeProduct'),
            array(), '', false
        );

        $resource->expects($this->once())
            ->method('loadByFakeProduct')
            ->with($subscriptionModel, 11)
            ->will($this->returnValue($subscriptionModel));


        $subscriptionModel->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource));


        $this->assertInstanceOf(
            'Tele2_Subscription_Model_Subscription',
            $subscriptionModel->loadByFakeProduct(11)
        );
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForSubscriptionGroup()
    {
        $groupModel = $this->getMock(
            'Tele2_Subscription_Model_Group',
            array('load'),
            array(), '', false
        );

        $groupModel->expects($this->once())
            ->method('load')
            ->with(1)
            ->will($this->returnValue($groupModel));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('tele2_subscription/group', array())
            ->will($this->returnValue($groupModel));

        return $config;
    }



    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForConfigs()
    {
        $configRelationCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_ConfigRelation_Collection',
            array('addFieldToFilter'),
            array(), '', false
        );

        $configRelationCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('subscription_entity_id', 11)
            ->will($this->returnValue($configRelationCollection));

        $configRelationModel = $this->getMock(
            'Tele2_Subscription_Model_ConfigRelation',
            array('getCollection'),
            array(), '', false
        );

        $configRelationModel->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($configRelationCollection));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('tele2_subscription/configRelation', array())
            ->will($this->returnValue($configRelationModel));

        return $config;
    }


    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForAddonCollection()
    {
        $productCollection = $this->getMock(
            'Mage_Catalog_Model_Resource_Product_Collection',
            array('addAttributeToSelect', 'addIdFilter'),
            array(), '', false
        );

        $productCollection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('*')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addIdFilter')
            ->with(array(1, 2, 3))
            ->will($this->returnValue($productCollection));

        $productModel = $this->getMock(
            'Mage_Catalog_Model_Product',
            array('getCollection'),
            array(), '', false
        );

        $productModel->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($productCollection));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('catalog/product', array())
            ->will($this->returnValue($productModel));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForAddons()
    {
        $addonRelationCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_AddonRelation_Collection',
            array('addFieldToFilter'),
            array(), '', false
        );

        $addonRelationCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('subscription_id', 11)
            ->will($this->returnValue($addonRelationCollection));


        $addonRelationModel = $this->getMock(
            'Tele2_Subscription_Model_AddonRelation',
            array('getCollection'),
            array(), '', false
        );

        $addonRelationModel->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($addonRelationCollection));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('tele2_subscription/addonRelation', array())
            ->will($this->returnValue($addonRelationModel));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForSaveAssocProducts()
    {
        $relationResource = $this->getMock(
            'Tele2_Subscription_Model_Resource_Relation',
            array('getSubscriptionProductIds'),
            array(), '', false);

        $relationResource->expects($this->any())
            ->method('getSubscriptionProductIds')
            ->with(11)
            ->will($this->returnValue(array(1, 4, 5)));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance'),
            array(), '', false);


        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/relation')
            ->will($this->returnValue($relationResource));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @param array $bindingParams
     * @param array $removeBinding
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForSaveBinding($bindingParams, $removeBinding)
    {
        $phpunitFramework = $this;

        $bindingCallback = function () use ($phpunitFramework, $bindingParams)
        {
            static $numberExecution = 0;

            if ($numberExecution == 0) {
                $binding = $phpunitFramework->getMock(
                    'Tele2_Subscription_Model_Binding',
                    array(
                        'setSubscriptionId', 'setTime', 'setArticleId', 'setMonthlyPriceWithVat',
                        'setMonthlyPriceWithoutVat', 'save'
                    ),
                    array(), '', false
                );

                $binding->expects($phpunitFramework->once())
                    ->method('setSubscriptionId')
                    ->with(11)
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('setTime')
                    ->with($bindingParams['time'])
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('setArticleId')
                    ->with($bindingParams['article_id'])
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('setMonthlyPriceWithVat')
                    ->with($bindingParams['monthly_price_with_vat'])
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('setMonthlyPriceWithoutVat')
                    ->with($bindingParams['monthly_price_without_vat'])
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('save')
                    ->will($phpunitFramework->returnValue($binding));
            } else {
                $binding = $phpunitFramework->getMock(
                    'Tele2_Subscription_Model_Binding', array('load', 'delete'), array(), '', false
                );

                $binding->expects($phpunitFramework->once())
                    ->method('load')
                    ->with($numberExecution)
                    ->will($phpunitFramework->returnValue($binding));

                $binding->expects($phpunitFramework->once())
                    ->method('delete')
                    ->will($phpunitFramework->returnValue($binding));

            }
            $numberExecution++;
            return $binding;
        };

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $config->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback($bindingCallback));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForBindings()
    {
        $bindingCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_Binding_Collection',
            array('count', 'getIterator', 'filterBySubscription'),
            array(), '', false
        );

        $bindingCollection->expects($this->once())
            ->method('filterBySubscription')
            ->with(11)
            ->will($this->returnValue($bindingCollection));

        $bindingCollection->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $binding = new Tele2_Subscription_Model_Binding();
        $binding->setTime(mktime(22, 0, 0, 2, 7, 2013));
        $binding->setId(1);

        $bindingCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($binding))));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/binding_collection', array())
            ->will($this->returnValue($bindingCollection));

        return $config;
    }


    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForSubscriptionsByProduct()
    {
        $relationCollection = $this->getMock(
            'Tele2_Subscription_Model_Resource_Relation_Collection',
            array('addFieldToFilter', 'addFieldToSelect', 'getColumnValues'),
            array(), '', false
        );

        $relationCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('product_id', 11)
            ->will($this->returnValue($relationCollection));

        $relationCollection->expects($this->once())
            ->method('addFieldToSelect')
            ->with('subscription_id')
            ->will($this->returnValue($relationCollection));

        $relationCollection->expects($this->once())
            ->method('getColumnValues')
            ->with('subscription_id')
            ->will($this->returnValue(array(1 => 1)));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance', 'getModelInstance'),
            array(), '', false);

        $subscription = $this->getMock(
            'Tele2_Subscription_Model_Resource_Relation_Collection',
            array('load'),
            array(), '', false
        );

        $subscription->expects($this->once())
            ->method('load')
            ->with(1)
            ->will($this->returnValue($subscription));

        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('tele2_subscription/subscription')
            ->will($this->returnValue($subscription));

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/relation_collection', array())
            ->will($this->returnValue($relationCollection));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForSubscriptionProductsCollection()
    {
        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getModelInstance'),
            array(), '', false);

        $phpunitFramework = $this;

        $mockGetModelInstance = function ($modelClass) use ($phpunitFramework)
        {
            $attributeSetId = 11;

            switch ($modelClass) {
                case 'eav/entity_attribute_set':
                    $entityAttributeSet = $phpunitFramework->getMock(
                        'Mage_Eav_Model_Entity_Attribute_Set',
                        array('load', 'getAttributeSetId'),
                        array(), '', false
                    );
                    $entityAttributeSet->expects($phpunitFramework->once())
                        ->method('load')
                        ->with(Tele2_Install_Helper_Data::ATTR_SET_SUBSCRIPTION, 'attribute_set_name')
                        ->will($phpunitFramework->returnValue($entityAttributeSet));

                    $entityAttributeSet->expects($phpunitFramework->once())
                        ->method('getAttributeSetId')
                        ->will($phpunitFramework->returnValue($attributeSetId));

                    return $entityAttributeSet;
                    break;

                case 'catalog/product':
                    $product = $phpunitFramework->getMock(
                        'Mage_Catalog_Model_Product',
                        array('getCollection'),
                        array(), '', false
                    );

                    $productCollection= $phpunitFramework->getMock(
                        'Mage_Catalog_Model_Resource_Product_Collection',
                        array('addAttributeToSelect', 'addFieldToFilter'),
                        array(), '', false
                    );

                    $productCollection->expects($phpunitFramework->once())
                        ->method('addAttributeToSelect')
                        ->with('*')
                        ->will($phpunitFramework->returnValue($productCollection));

                    $productCollection->expects($phpunitFramework->once())
                        ->method('addFieldToFilter')
                        ->with('attribute_set_id', $attributeSetId)
                        ->will($phpunitFramework->returnValue($productCollection));


                    $product->expects($phpunitFramework->once())
                        ->method('getCollection')
                        ->will($phpunitFramework->returnValue($productCollection));

                    return $product;
                    break;
            }
        };

        $config->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback($mockGetModelInstance));

        return $config;
    }

    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForRelatedProducts()
    {
        $productCollection = $this->getMock(
            'Mage_Catalog_Model_Resource_Product_Collection',
            array(
                'addAttributeToSelect', 'addMinimalPrice', 'addFinalPrice', 'addTaxPercents',
                'addStoreFilter', 'addIdFilter'
            ),
            array(), '', false
        );

        $productCollection->expects($this->any())
            ->method('addAttributeToSelect')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addMinimalPrice')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addFinalPrice')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addTaxPercents')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addStoreFilter')
            ->with(1)
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('addIdFilter')
            ->with(array(1,2,3))
            ->will($this->returnValue($productCollection));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance'),
            array(), '', false);

        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('catalog/product_collection', array())
            ->will($this->returnValue($productCollection));

        return $config;
    }


    /**
     * Get a config mocked model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getMockedConfigForRelatedProductsIds()
    {
        $relation = $this->getMock(
            'Tele2_Subscription_Model_Resource_Relation_Collection',
            array('getSubscriptionProductIds'),
            array(), '', false
        );

        $relation->expects($this->once())
            ->method('getSubscriptionProductIds')
            ->with(11)
            ->will($this->returnValue(array(1,2,3)));

        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getResourceModelInstance'),
            array(), '', false);


        $config->expects($this->once())
            ->method('getResourceModelInstance')
            ->with('tele2_subscription/relation', array())
            ->will($this->returnValue($relation));

        return $config;
    }
}
