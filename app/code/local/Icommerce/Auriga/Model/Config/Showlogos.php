<?php

class Icommerce_Auriga_Model_Config_Showlogos
{

    public function toOptionArray()
    {
        return array(
       		#array('value'=>'DIBS', 'label'=>Mage::helper('adminhtml')->__('DIBS trusted')),
       		array('value'=>NULL, 'label'=>NULL),
       		array('value'=>'VISA_SECURE', 'label'=>Mage::helper('adminhtml')->__('Verified by VISA')),
       		array('value'=>'MC_SECURE', 'label'=>Mage::helper('adminhtml')->__('MasterCard SecureCode')),
       		array('value'=>'JCB_SECURE', 'label'=>Mage::helper('adminhtml')->__('JCB J/Secure')),
       		array('value'=>'PCI', 'label'=>Mage::helper('adminhtml')->__('PCI')),
    		array('value'=>'AMEX', 'label'=>Mage::helper('adminhtml')->__('American Express')),
    		array('value'=>'BAX', 'label'=>Mage::helper('adminhtml')->__('BankAxess')),
    		array('value'=>'DIN', 'label'=>Mage::helper('adminhtml')->__('Diners Club')),
    		array('value'=>'DK', 'label'=>Mage::helper('adminhtml')->__('Dankort')),
    		array('value'=>'FFK', 'label'=>Mage::helper('adminhtml')->__('Forbrugsforeningen Card')),
    		array('value'=>'JCB', 'label'=>Mage::helper('adminhtml')->__('JCB (Japan Credit Bureau)')),
    		array('value'=>'MC', 'label'=>Mage::helper('adminhtml')->__('MasterCard')),
    		array('value'=>'MTRO', 'label'=>Mage::helper('adminhtml')->__('Maestro')),
    		array('value'=>'MOCA', 'label'=>Mage::helper('adminhtml')->__('Mobilcash')),
    		array('value'=>'VISA', 'label'=>Mage::helper('adminhtml')->__('Visa')),
    		array('value'=>'ELEC', 'label'=>Mage::helper('adminhtml')->__('Visa Electron')),
    		array('value'=>'AKTIA', 'label'=>Mage::helper('adminhtml')->__('Aktia Web Payment')),
    		array('value'=>'DNB', 'label'=>Mage::helper('adminhtml')->__('Danske Netbetaling (Danske Bank)')),
    		array('value'=>'EDK', 'label'=>Mage::helper('adminhtml')->__('eDankort')),
    		array('value'=>'ELV', 'label'=>Mage::helper('adminhtml')->__('Bank Einzug (eOLV)')), 
    		array('value'=>'EW', 'label'=>Mage::helper('adminhtml')->__('eWire')),
    		array('value'=>'FSB', 'label'=>Mage::helper('adminhtml')->__('Swedbank Direktbetalning')),
    		array('value'=>'GIT', 'label'=>Mage::helper('adminhtml')->__('Getitcard')),
    		array('value'=>'ING', 'label'=>Mage::helper('adminhtml')->__('ING iDeal Payment')),
    		array('value'=>'SEB', 'label'=>Mage::helper('adminhtml')->__('SEB Direktbetalning')),
    		array('value'=>'SHB', 'label'=>Mage::helper('adminhtml')->__('SHB Direktbetalning')),
    		array('value'=>'SOLO', 'label'=>Mage::helper('adminhtml')->__('Nordea')),
    		array('value'=>'VAL', 'label'=>Mage::helper('adminhtml')->__('Valus')),		

        );
    }

}
