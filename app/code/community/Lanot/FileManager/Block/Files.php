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
 * @category    Lanot
 * @package     Lanot_FileManager
 * @copyright   Copyright (c) 2012 Lanot
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Lanot_FileManager_Block_Files
    extends Lanot_FileManager_Block_Adminhtml_Content_Files
{

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }

    /**
     * Get related online documents.
     *
     * @return mixed
     */
    public function getRelatedOnlineDocuments()
    {
        $relatedDocuments = Mage::getModel('lanot_filemanager/FileStorage')
            ->getCollection()->getFilesByProductId($this->getProduct()->getId());

        $files = array();
        foreach ($relatedDocuments as $document) {
            $fileName = $document->getFilename();
            $filePath = $document->getDirectory();
            $files[] = new Varien_Object(
                array(
                    'filename' => $filePath . DS . $fileName,
                    'basename' => basename($filePath . DS . $fileName),
                    'mtime'    => filemtime($filePath . DS . $fileName),
                    'file_url' => str_replace(Mage::getBaseDir(), '', $filePath) . DS .  $fileName
                )
            );

        }

        return $files;
    }
}
