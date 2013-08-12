<?php
class Tele4G_Import_Model_Convert_Parser_Xml extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    protected $_attributeMap = array(
        'autofocus'=>'Autofokus',
        'rearcamera'=>'Bakåtriktad kamera',
        'batterycapacity'=>'Batterikapacitet',
        'bluetooth'=>'Blåtand',
        'bluetoothversion'=>'Blåtandsversion',
        'chipset'=>'Chipset',
        'datarate'=>'Datahastighet',
        'sharpestcameraphone'=>'De vassaste kameratelefonerna',
        'latestandhottest'=>'Det senaste och hetaste',
        'dimensions'=>'Dimensioner',
        'dlna'=>'DLNA',
        'dualcoreprocessor'=>'Dual core processor',
        'dualcoreprocessor'=>'Dual core processor',
        'simplephonewithbuttons'=>'En enkel telefon med knappar',
        'email'=>'E-post',
        'flightmode'=>'Flight mode',
        'fmradio'=>'FM-radio',
        'frontcamera'=>'Framåtriktad kamera',
        'warranty'=>'Garantitid',
        'gps'=>'GPS',
        'hdmi'=>'HDMI',
        'ios'=>'IOS',
        'cameraover5mpix'=>'Kamera över 5 Mpix',
        'cameraflash'=>'Kamerablixt',
        'rearcameraresolution'=>'Kameraupplösning (bak)',
        'frontcameraresolution'=>'Kameraupplösning (fram)',
        'classickeypad'=>'Klassisk knappsats',
        'mediatypessupported'=>'Mediatyper som stödjs',
        'memorycardslot'=>'Minneskortsplats',
        'memorycardtype'=>'Minneskorttyp',
        'mms'=>'MMS',
        'mp3player'=>'MP3-spelare',
        'navigationgpssoftincluded'=>'Navigationsmjukvara för GPS ingår',
        'networkbands2g'=>'Nätverksband 2G',
        'networkbands3g'=>'Nätverksband 3G',
        'networkbands4g'=>'Nätverksband 4G',
        'operatingsystemversion'=>'Operativsystemversion',
        'operatorlocked'=>'Operatörslåst',
        'touchscreen'=>'Pekskärm',
        'processorspeed'=>'Processorhastighet',
        'qwertykeypad'=>'QWERTY knappsats',
        'qwertykeypad'=>'QWERTY knappsats',
        'ram'=>'RAM-minne (arbetsminne)',
        'rom'=>'ROM-minne (lagringsminne)',
        'talk'=>'Samtalstid',
        'singlecoreprocessor'=>'Single core processor',
        'screensize'=>'Skärmstorlek',
        'screenresolution'=>'Skärmupplösning',
        'smartphone'=>'Smartphone',
        'standbytime'=>'Standbytid',
        'shockandwaterresistant'=>'Stöt- och vattentålig',
        'symbian'=>'Symbian',
        'flashtype'=>'Typ av blixt',
        'flashtype'=>'Typ av blixt',
        'typeofgps'=>'Typ av GPS',
        'typeofdisplay'=>'Typ av skärm',
        'videoinhd'=>'Video i HD',
        'videoplayback'=>'Videouppspelning',
        'wifiwlan'=>'WiFi/WLAN',
        'windowsphone'=>'Windows Phone',
        'wlanstandard'=>'WLAN standard',
        'otherconnections'=>'Övriga anslutningar',
        'admission'=>'Inträdesavgift',
        'monthlyfee'=>'Månadsavgift',
        'callstotele2comviq'=>'Samtal till Tele2/Comviq-nätet/min',
        'calltoothernetworksweden'=>'Samtal till övriga mobilnät i Sverige/min',
        'openingfee'=>'Öppningsavgift',
        'smstotele2comviqst'=>'SMS till Tele2/Comviq/st',
        'smstotele2comviqst'=>'SMS till Tele2/Comviq/st',
        'smstoothernetworksweden'=>'SMS till övriga mobilnät i Sverige/st',
        'mmstotele2comviqst'=>'MMS till Tele2/Comviq/st',
        'mmstoothernetworksweden'=>'MMS till övriga mobilnät i Sverige/st',
        'videocall'=>'Videosamtal',
        'callabroad'=>'Samtal till utlandet',
        'voicemailmin'=>'Telefonsvararen/min',
        'directdebitoreinvoice'=>'Autogiro eller e-faktura',
        'paperinvoicepc'=>'Pappersfaktura/st',
        'datavolumeincluded'=>'Datavolym som ingår',
        'speedupto'=>'Hastighet upp till',
        'freeminutestoallworkpcs'=>'Antal fria minuter till alla operat¿rer',
        'freesmstoanynetwork'=>'Antal fria SMS till alla operat¿rer',
        'freemmstoallworkpieces'=>'Antal fria MMS till alla operat¿rer',
    );

    protected function _xml2array($xml) {
        $array = json_decode(json_encode($xml), true);

        foreach ( array_slice($array, 0) as $key => $value ) {
            if ( empty($value) ) $array[$key] = null;
            elseif ( is_array($value) ) $array[$key] = $this->_xml2array($value);
        }

        return $array;
    }

    protected function _getXmlString()
    {
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();
        $batchIoAdapter->open(false);

        $xmlString = '';
        while (($fileString = $batchIoAdapter->read()) !== false) {
            $xmlString .= $fileString;
        }

        return $xmlString;
    }

    public function parse()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $xmlString = $this->_getXmlString();

        $itemType = $this->getVar('attributeset', 'Default');
        switch ($itemType)  {
            case 'subscription':
                $itemCount = $this->_parseSubscriptions($xmlString);
                break;
            case 'device':
                $itemCount = $this->_parseArticles($xmlString);
                break;
            default:
                $itemCount = 0;
                break;

        }

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $itemCount));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    public function acccompat()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $xmlString = $this->_getXmlString();

        $itemCount = $this->_parseAccessoryCompatibilities($xmlString);

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $itemCount));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    public function addcompat()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $xmlString = $this->_getXmlString();

        $itemCount = $this->_parseAddonCompatibilities($xmlString);

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $itemCount));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    public function variants()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $xmlString = $this->_getXmlString();

        $itemCount = $this->_parseHardwareVariants($xmlString);

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $itemCount));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    public function groups()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $groups = $this->_getGroups();

        foreach ($groups as $group) {
            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(serialize($group))
                ->setStatus(1)
                ->save();
        }


        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', count($groups)));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    protected function _getGroups()
    {
        $groups = array();

        $xml = simplexml_load_string($this->_getXmlString());

        $variants = $xml->hardwareVariants[0][0]->hardwareVariantGroup;

        foreach ($variants as $variant) {
            $variant = $this->_xml2array($variant);
            $groups[$variant['masterArticleId']]['groupId'] = $variant['id'];
            $groups[$variant['masterArticleId']]['masterArticleId'] = $variant['masterArticleId'];
            $groups[$variant['masterArticleId']]['associates'] = array();
            if (is_array($variant['articleIds'])) {
                foreach ($variant['articleIds'] as $key => $val) {
                    $groups[$variant['masterArticleId']]['associates'] = $val;
                }
            }

        }

        $accessoryCompatibilities = $xml->accessoryCompatibilities[0][0]->compatibility;
        $accessoryCompatibilityGroups = array();

        foreach ($accessoryCompatibilities as $accessoryCompatibility) {
            $accessoryCompatibility = $this->_xml2array($accessoryCompatibility);
            if (array_key_exists($accessoryCompatibility['deviceId'], $accessoryCompatibilityGroups)) {//device was registered. just add an accessory
                $accessoryCompatibilityGroups[$accessoryCompatibility['deviceId']][] = $accessoryCompatibility['accessoryId'];
            } else {
                $accessoryCompatibilityGroups[$accessoryCompatibility['deviceId']] = array($accessoryCompatibility['accessoryId']);
            }
        }

        foreach ($groups as $masterArticleId => $groupArray) {
            $masterAccessories = array();
            foreach ($groupArray['associates'] as $key => $associatedArticleId) {
                if (isset($accessoryCompatibilityGroups[$associatedArticleId])) {
                    $associatedAccessories = $accessoryCompatibilityGroups[$associatedArticleId];
                    $masterAccessories = array_merge($masterAccessories, $associatedAccessories);
                    $groups[$masterArticleId]['accessories'] = $masterAccessories;
                }
            }
        }

        return $groups;
    }

    protected function _parseSubscriptions($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        $subscriptions = $xml->subscriptions[0][0]->subscription;
        $itemCount = 0;
        foreach ($subscriptions as $subscription) {
            $subscription = $this->_xml2array($subscription);
            $subscription['type'] = $subscription['subscriptionType'];

            if(isset($subscription['priceAttributes']['attribute'])){
                foreach($subscription['priceAttributes']['attribute'] as $attribute){
                    $mapKey = array_search($attribute['key'], $this->_attributeMap);
                    if(($mapKey !== false) && isset($attribute['value'])){
                        $subscription[$mapKey] = $attribute['value'];
                    }
                }
            }

            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(array('serialized'=>serialize($subscription)))
                ->setStatus(1)
                ->save();

            $itemCount++;
        }

        return $itemCount;
    }

    protected function _parseArticles($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        $articles = $xml->articles[0][0]->article;
        $itemCount = 0;
        foreach ($articles as $article) {
            $article = $this->_xml2array($article);
            $article['type'] = $article['@attributes']['type'];

            if(isset($article['technicalSpecifications']['attribute'])){
                foreach($article['technicalSpecifications']['attribute'] as $attribute){
                    $mapKey = array_search($attribute['key'], $this->_attributeMap);
                    if(($mapKey !== false) && isset($attribute['value'])){
                        $article[$mapKey] = $attribute['value'];
                        $article['ss4_' . $mapKey] = $attribute['value'];
                    }
                }
            }

            foreach ($article as $fieldName => $fieldValue) {
                if (strpos($fieldName, 'ss4_') === false) {
                    $article['ss4_' . $fieldName] = $fieldValue;
                }
            }

            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(array('serialized'=>serialize($article)))
                ->setStatus(1)
                ->save();

            $itemCount++;
        }

        return $itemCount;
    }

    protected function _parseAccessoryCompatibilities($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        $accessoryCompatibilities = $xml->accessoryCompatibilities[0][0]->compatibility;
        $accessoryCompatibilityGroups = array();

        foreach ($accessoryCompatibilities as $accessoryCompatibility) {
            $accessoryCompatibility = $this->_xml2array($accessoryCompatibility);
            if (array_key_exists($accessoryCompatibility['deviceId'], $accessoryCompatibilityGroups)) {//device was registered. just add an accessory
                $accessoryCompatibilityGroups[$accessoryCompatibility['deviceId']][] = $accessoryCompatibility['accessoryId'];
            } else {
                $accessoryCompatibilityGroups[$accessoryCompatibility['deviceId']] = array($accessoryCompatibility['accessoryId']);
            }
        }

        $itemCount = 0;
        foreach ($accessoryCompatibilityGroups as $deviseId => $accessoriesIds) {
            $group = array(
                'type'=>'ACCESSORY_COMPATIBILITY',
                'device'=>$deviseId,
                'accessories'=>implode(',', $accessoriesIds)
            );

            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(array('serialized'=>serialize($group)))
                ->setStatus(1)
                ->save();

            $itemCount++;
        }

        return $itemCount;
    }

    protected function _parseAddonCompatibilities($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        $plusServiceCompatibilities = $xml->plusServiceCompatibilities[0][0]->compatibility;

        $itemCount = 0;
        foreach ($plusServiceCompatibilities as $plusServiceCompatibility) {
            $plusServiceCompatibility = $this->_xml2array($plusServiceCompatibility);
            $plusServiceCompatibility['type'] = 'ADDON_COMPATIBILITY';

            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(array('serialized'=>serialize($plusServiceCompatibility)))
                ->setStatus(1)
                ->save();

            $itemCount++;
        }

        return $itemCount;
    }

    public function addongroups()
    {
        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();

        $groups = $this->_getAddonGroups();

        foreach ($groups as $group) {
            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(serialize($group))
                ->setStatus(1)
                ->save();
        }

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', count($groups)));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    protected function _getAddonGroups()
    {
        $xml = simplexml_load_string($this->_getXmlString());

        $addonGroups = $xml->plusServiceGroups[0][0]->plusServiceGroup;

        $groups = array();
        foreach ($addonGroups as $addonGroup) {
            $addonGroup = $this->_xml2array($addonGroup);
            $groups[$addonGroup['name']] = array(
                'id'=>$addonGroup['id'],
                'name'=>$addonGroup['name'],
            );
            if (is_array($addonGroup['articleIds'])) {
                foreach ($addonGroup['articleIds'] as $key => $addonArticleIds) {
                    $groups[$addonGroup['name']]['addons'] = $addonArticleIds;
                }
            }
        }

        return $groups;
    }

    protected function _parseHardwareVariants($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        $hardwareVariantGroups = $xml->hardwareVariants[0][0]->hardwareVariantGroup;

        $itemCount = 0;
        foreach ($hardwareVariantGroups as $hardwareVariantGroup) {
            $hardwareVariantGroup = $this->_xml2array($hardwareVariantGroup);
            $hardwareVariantGroup['type'] = 'HARDWARE_VARIANT_GROUP';

            $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData(array('serialized'=>serialize($hardwareVariantGroup)))
                ->setStatus(1)
                ->save();

            $itemCount++;
        }

        return $itemCount;
    }

    public function unparse()
    {
        return $this;
    }
}