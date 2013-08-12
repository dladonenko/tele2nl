<?php 
    $installer = $this;
    $installer->startSetup();

    $installer->getConnection()
        ->addColumn($installer->getTable('sales/order'), 'auriga_transaction_id',
            array(
                'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
                'required'      => false,
                'label'         => 'Auriga Transaction ID',
                'sort_order'    => 90,
                'global'	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'visible'	=> true,
                'user_defined'	=> true,
                'comment'       => 'Auriga Transaction ID',
            )
        );
    $installer->getConnection()
        ->addColumn($installer->getTable('sales/order'), 'auriga_captured',
            array(
                'type'          => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'required'      => false,
                'label'         => 'Auriga Captured',
                'sort_order'    => 90,
                'global'	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'visible'	=> true,
                'user_defined'	=> true,
                'comment'       => 'Auriga Captured',
            )
        );
    $installer->getConnection()
        ->addColumn($installer->getTable('sales/order'), 'auriga_customer_ref_num',
            array(
                'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'        => '64',
                'required'      => false,
                'sort_order'    => 90,
                'global'	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'visible'	=> true,
                'user_defined'	=> true,
                'comment'       => 'Auriga Customer Reference Number',
            )
        );

    $installer->endSetup();      
      