<?php
class Tele2_Catalog_Model_Product_Attribute_Backend_Splash
    extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Image
{
    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = Mage::getStoreConfig('telco_splash/splash');
        $splashesArray = array();
        $splashes = array();
        if (count($options)) {
            foreach ($options as $id => $option) {
                preg_match('/^([a-z0-9]*)_(title|image)$/', $id, $matches);
                if (isset($matches[1]) && isset($matches[2])) {
                    if (isset($splashesArray[$matches[1]])) {
                        $splashesArray[$matches[1]][$matches[2]] = $option;
                    } else {
                        $splashesArray[$matches[1]] = array(
                            'value' => $matches[1],
                            'label' => $option,
                            $matches[2] => $option
                        );
                    }
                }
            }
        }
        $splashes[] = array(
            'value' => '',
            'label' => '-- Please Select --'
        );
        foreach ($splashesArray as $splash) {
            if (
                isset($splash['label']) && $splash['label'] &&
                isset($splash['value']) && $splash['value']
            ) {
                $splashes[] = $splash;
            }
        }
        
        return $splashes;
    }
    
    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null,
        );

        $column['type']     = Varien_Db_Ddl_Table::TYPE_VARCHAR;
        $column['size']     = '8';
        $column['nullable'] = true;
        $column['comment']  = 'Enterprise Giftcard Type ' . $attributeCode . ' column';

        return array($attributeCode => $column);
    }
    
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceSingleton('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
    
    public function getOptionText()
    {
        return "SPLASH OptionText";
    }
}
