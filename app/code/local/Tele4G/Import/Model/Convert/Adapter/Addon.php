<?php
class Tele4G_Import_Model_Convert_Adapter_Addon extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function saveRow($importedItemObject)
    {
        $importedItemObject = unserialize($importedItemObject);

        if (is_array($importedItemObject['addons']) && count($importedItemObject['addons'])) {
            $addonModel = Mage::getModel('catalog/product');

            $addonGroupAttribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'addon_group');

            $values = array();
            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($addonGroupAttribute->getId())
                ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID, false)
                ->load();

            foreach ($valuesCollection as $item) {
                $values[$item->getValue()] = $item->getId();
            }

            foreach ($importedItemObject['addons'] as $key => $addonArticleId) {
                $addon = $addonModel->loadByAttribute('articleid', $addonArticleId);
                if ($addon && $addon->getId()) {
                    $addon->setAddonGroup($values[$importedItemObject['name']]);
                    $addon->save();
                }
            }
        }

        return true;
    }

    public function load()
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }
}
