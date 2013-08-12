<?php
/**
 * Tele2 subscription module
 *
 * @category    Tele2
 * @package     Tele2_Subscription
 */
class Tele2_Subscription_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PATH_TO_SUBSCRIPTION_CONFIG_IMAGE = 'catalog/subscription/config';
    
    /*
     * get subscription Id from sku
     */
    public function getSubscriptionIdBySky($sku) 
    {
        if (preg_match('%subscr-(\d+)-(\d+)%',  $sku, $foundSubscription)) {
            if (is_array($foundSubscription)) {
                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($foundSubscription[1]);
                if (count($_subscription))
                    return $_subscription;
            }
        }
        return false;
    }
    
    public function getSubscriptionBySku($sku)
    {
        if (preg_match('%subscr-(\d+)-(\d+)%',  $sku, $foundSubscription)) {
            if (is_array($foundSubscription)) {
                $_subscription = Mage::getModel('tele2_subscription/mobile')->load($foundSubscription[1]);
                if (count($_subscription))
                    $_subscription->setParamBindPeriod($foundSubscription[2]);
                    return $_subscription;
            }
        }
        return false;
    }

    public function generateCustomOptions($productId, $subscription)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $options = $product->getOptions();

        $subscriptionOptionId = null;
        if ($options) {
            
            foreach ($options as $option) {
                if ($option->getDefaultTitle() == 'subscriptions') {
                    $subscriptionOptionId = $option->getId();
                    $subscription->modifyOption($option, $product, $subscription->getId());
                }
            }
        }

        if (!$subscriptionOptionId) {
            $subscriptionOptionId = $subscription->generateOption($product);
        }
    }

    public function unlinkProducts($productIds, $subscriptionId)
    {
        foreach ($productIds as $id)
        {
            Mage::getResourceModel('tele2_subscription/relation')
              ->unlinkProduct($id, $subscriptionId);
        }
    }

    public function uploadImage($imageFileName, $path = null, $allowExtentions = null)
    {
        if (!$path) {
            $path = self::PATH_TO_SUBSCRIPTION_CONFIG_IMAGE;
        }
        if (!$allowExtentions) {
            $allowExtentions = array('jpg','jpeg','gif','png');
        }
        $image = $_FILES[$imageFileName];
        if (isset($image['name']) && $image['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader($imageFileName);

                $uploader->setAllowedExtensions($allowExtentions);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);

                // Set media as the upload dir
                $media_path  = Mage::getBaseDir('media') . DS . $path . DS;

                // Upload the image
                $uploader->save($media_path, $image['name']);

                return (string)($path . $uploader->getUploadedFileName());
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                //return $this;
            }
        } else {
            $image_main = Mage::app()->getRequest()->getPost($imageFileName);
            if(isset($image_main['delete']) && $image_main['delete'] == 1) {
                return '';
            } else {
                return null;
            }
        }
    }

    /**
     * Returns image url. The image will be resized if dimensions are specified
     * @param $path string Path to image id DB
     * @param int $width
     * @param int $height
     * @return string Url for rendering the image
     */
    public function getImageUrl($path, $width=0, $height=0)
    {
        if ($path && file_exists(Mage::getBaseDir('media') . DS . $path)) {
            if (is_int($width) &&  is_int($height) && $width>0 && $height>0) {

                $pathParts = explode('/', $path);
                $fileName = $pathParts[count($pathParts)-1];
                $dir = str_replace($fileName, '', $path);
                $resizedImagePath = $dir . $width . 'x' . $height . DS . $fileName;
                $resizedImagePathFull = Mage::getBaseDir('media') . DS . $resizedImagePath;
                $resizedImageUrl = Mage::getBaseUrl('media') . DS . $resizedImagePath;

                if (file_exists($resizedImagePathFull)) {
                    return $resizedImageUrl;
                }

                $resizedImage = new Varien_Image(Mage::getBaseDir('media') . DS . $path);
                $resizedImage->constrainOnly(true);
                $resizedImage->keepAspectRatio(true);
                $resizedImage->keepFrame(true);
                $resizedImage->resize($width, $height);
                $resizedImage->save($resizedImagePathFull);

                return $resizedImageUrl;
            }
            //do not resize if dimensions are not specified
            return Mage::getBaseUrl('media') . DS . $path;
        }

        return '';
    }
}
