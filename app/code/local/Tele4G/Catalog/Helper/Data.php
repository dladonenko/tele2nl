<?php
class Tele4G_Catalog_Helper_Data extends Tele2_Catalog_Helper_Data
{
    /**
     * @param $config
     * @return array
     */
    public function getArrayFromConfig($config = null)
    {
        $result = array();
        if ($config) {
            $_values = Mage::getStoreConfig($config);
            if ($_values) {
                $_valuesArray = explode(",", $_values);
                $result = array_map('trim', $_valuesArray);
            }
        }
        return $result;
    }
}
