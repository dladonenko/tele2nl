<?php
class Icommerce_Auriga_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('icommerce/auriga/form.phtml');
        parent::_construct();
    }
}
