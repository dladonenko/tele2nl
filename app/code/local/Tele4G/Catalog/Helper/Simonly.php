<?php
class Tele4G_Catalog_Helper_Simonly extends Tele2_Catalog_Helper_Data
{
    const SKU_EREG = '/([0-9]+)\;([_a-zA-Z]+);([\.0-9]+);([0-9]+)/';
    protected $_params = array();

    public function hasParams($sku = null)
    {
        if ($sku && preg_match(self::SKU_EREG, $sku, $regs)) {
            return $this->_params[$sku] = $regs;
        }
        return false;
    }

    public function getSimType($sku = null)
    {
        if (!isset($this->_params[$sku])) {
            $this->hasParams($sku);
        }
        if (isset($this->_params[$sku])) {
            return $this->_params[$sku][2];
        }
        return false;
    }

    public function getArticleId($sku = null)
    {
        if (!isset($this->_params[$sku])) {
            $this->hasParams($sku);
        }
        if (isset($this->_params[$sku])) {
            return $this->_params[$sku][1];
        }
        return false;
    }

    public function getMonthlyPriceWithVat($sku = null)
    {
        if (!isset($this->_params[$sku])) {
            $this->hasParams($sku);
        }
        if (isset($this->_params[$sku])) {
            return $this->_params[$sku][3];
        }
        return false;
    }

    public function getBindingPeriod($sku = null)
    {
        if (!isset($this->_params[$sku])) {
            $this->hasParams($sku);
        }
        if (isset($this->_params[$sku])) {
            return $this->_params[$sku][4];
        }
        return false;
    }
}
