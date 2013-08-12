<?php
/**
 * @author Branko Ajzele <branko@inchoo.net>
 * @copyright Adform 
 */
class Adform_Track_Block_Adminhtml_Edit_Renderer_Url_Key extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $url = Mage::getUrl('adform/feed/list', array('key'=>$row->getData($this->getColumn()->getIndex())));
        
        if (strlen($url) == (strrpos($url, '/') + 1)) {
            $url = substr($url, 0, strlen($url)-1) . '.xml';
        }
        
        $html = sprintf('<a href="%s" target="_blank">%s</a>', $url, $url);
      
        return $html;
    }
}
