<?php

class Tele4G_WidgetTest_Block_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface 
{
    protected $_serializer = null;
    
    protected function _construct()
    {
        $this->_serializer = new Varien_Object();
        parent::_construct();
    }
    
    protected function _toHtml()
    {
        $html = '';
        $config = $this->getData('enabled_services');
        if (empty($config)) {
            return $html;
        }
        $services = explode(',', $config);
        $list = array();
        foreach ($services as $service) {
            $item = $this->_generateServiceLink($service);
            if ($item) {
                $list[] = $item;
            }
        }
        $this->assign('list', $list);
        return parent::_toHtml();
    }
    
    protected function _generateServiceLink($service)
    {
        /**
         * Page title
         */
        $pageTitle = '';
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $pageTitle = $headBlock->getTitle();
        }

        /**
         * Current URL
         */
        $currentUrl = $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));

        /**
         * Link HTML
         */
        $attributes = array();
        $icon = '';
        switch ($service) {
            case 'facebook':
                $attributes = array(
                    'href'  => 'http://www.facebook.com/submit?url=' . rawurlencode($currentUrl) . '&amp;phase=2',
                    'title' => 'Facebook',
                );
                $icon = 'facebook32.png';
                break;
            case 'googleplus':
                $attributes = array(
                    'href'  => 'http://google.com/post?url=' . rawurlencode($currentUrl),
                    'title' => 'Add to del.icio.us',
                    'onclick'   => 'window.open(\'http://del.icio.us/post?v=4&amp;noui&amp;jump=close&amp;url='
                        . rawurlencode($currentUrl) . "&amp;title=" . rawurlencode($pageTitle)
                        . "','delicious', 'toolbar=no,width=700,height=400'); return false;",
                );
                $icon = 'googleplus32.png';
                break;
            case 'twitter':
                $attributes = array(
                    'href'      => 'http://twitter.com/home?status='
                        . rawurlencode('Currently reading ' . $pageTitle . ' at ' . $currentUrl ),
                    'title'     => 'Tweet This!',
                    'target'    => '_blank',
                );
                $icon = 'twitter32.png';
                break;
            default:
                return array();
                break;
        }

        $item = array(
            'text' => $attributes['title'],
            'attributes' => $this->_serializer->setData($attributes)->serialize(),
            'image' => $this->getSkinUrl("images/" . $icon),
        );

        return $item;
    }
}
