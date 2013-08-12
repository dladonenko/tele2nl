<?php
/**
 * AllProduct list toolbar block
 *
 * @category    Tele4G
 * @package     Tele4G_Subscription
 */
class Tele4G_Subscription_Block_AllProducts_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    /**
     * Init Toolbar
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_orderField  = Mage::getStoreConfig(
            Mage_Catalog_Model_Config::XML_PATH_LIST_DEFAULT_SORT_BY
        );

        $this->_availableOrder = $this->_getConfig()->getAttributeUsedForSortByArray();

        switch (Mage::getStoreConfig('tele4G_subscription/frontend/subscription/list_mode')) {
            case 'pre':
                $this->_availableMode = array('pre' => $this->__('Pre'));
                break;

            case 'post':
                $this->_availableMode = array('post' => $this->__('Post'));
                break;

            case 'pre-post':
                $this->_availableMode = array('pre' => $this->__('Pre'), 'post' =>  $this->__('Post'));
                break;

            case 'post-pre':
                $this->_availableMode = array('post' => $this->__('Post'), 'pre' => $this->__('Pre'));
                break;
        }
        $this->setTemplate('catalog/allProducts/product/list/toolbar.phtml');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChild('product_list_toolbar_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';
    }
}
