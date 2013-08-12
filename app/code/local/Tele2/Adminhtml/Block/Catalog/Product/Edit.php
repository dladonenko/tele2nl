<?php
class Tele2_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Preview'),
                    'onclick'   => "previewProduct('" . $this->getUrl('hardwarepreview/product/preview/id/' . $this->getRequest()->getParam('id')) . "');",
                    'class'  => 'add'
                ))
            );
        }

        return parent::_prepareLayout();
    }


    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('catalog/product/preview', array('_current'=>true));
}
}
