<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order Statuses source model
 */
class Icommerce_Auriga_Model_Auriga_Paymethod
{
    // set null to enable all possible
    protected $_stateStatuses = array(
        "Alla",
        "Kort",
        "Internetbank",
        "Medel på Aurigakonto",
        "Internetbank + Aurigakonto"
    );

    public function toOptionArray()
    {
        /*if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }*/
        $options = array();
        $options[] = array(
        	   'value' => '',
        	   'label' => Mage::helper('adminhtml')->__('-- Please Select --')
        	);
        foreach ($this->_stateStatuses as $ix=>$label) {
        	$options[] = array(
        	   'value' => $ix,
        	   'label' => $label
        	);
        }
        return $options;
    }
}