<?php

class Tele4G_WidgetTest_Model_Services extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'twitter', 'label' => 'Twitter'),
            array('value' => 'facebook', 'label' => 'Facebook'),
            array('value' => 'googleplus', 'label' => 'Google+'),
        );
    }
}