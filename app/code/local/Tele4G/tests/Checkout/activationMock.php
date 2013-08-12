<?php

class Activation_Mock 
{
    protected $_data = array();

    public function getOfferId()
    {
        return "000001";
    }
    
    public function getSimType()
    {
        return $this->_data['sim_type'];
    }

    public function setSimType($var)
    {
        $this->_data['sim_type'] = $var;
    }
    
    public function setActivationType($var)
    {
        $this->_data['activation_type'] = $var;
    }
    
    public function getActivationType()
    {
        return $this->_data['activation_type'];
    }
    
    public function setActivationNumber($var)
    {
        $this->_data['activation_number'] = $var;
    }

    public function getActivationNumber()
    {
        return $this->_data['activation_number'];
    }
    
    public function setActivationExistNumber($var)
    {
        $this->_data['activation_exist_number'] = $var;
    }
    
    public function getActivationExistNumber()
    {
        return $this->_data['activation_exist_number'];
    }
}