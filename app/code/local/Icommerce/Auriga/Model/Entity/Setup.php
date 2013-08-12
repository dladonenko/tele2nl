<?php 

class Icommerce_Auriga_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'order' => array(
                'entity_model'          => 'sales/order',
                'table'                 => 'sales/order',
                'increment_model'       => 'eav/entity_increment_numeric',
                'increment_per_store'   => 1,
                'attributes' => array(
                    'auriga_transaction_id' => array(
                        'type'          => 'varchar',
                        'required'      => false,
                        'label'         => 'Auriga Transaction ID',
                        'sort_order'    => 90,
                        'global'		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'		=> true,
                        'user_defined'	=> true,
                    ),
                    'auriga_captured' => array(
                        'type'          => 'int',
                        'required'      => false,
                        'label'         => 'Auriga Captured',
                        'sort_order'    => 90,
                        'global'		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'		=> true,
                        'user_defined'	=> true,
                    ),
                ),
            ),
        );
    }

}
