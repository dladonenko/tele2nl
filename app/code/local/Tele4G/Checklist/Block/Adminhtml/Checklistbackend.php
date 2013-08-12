<?php  

class Tele4G_Checklist_Block_Adminhtml_Checklistbackend extends Mage_Adminhtml_Block_Template {
    
    /**
     * return arra of all existing checks
     * @return array
     */
    public function checkList(){
        $array = array('SS4','GA','TrackingJS','Auriga','Smtp','StoreEmail','NAS','StaticPath','SoapApi','Database','Ip');
        return $array;        
    }
    
    public function checkSS4()
    {
        $ss4 = Mage::getConfig()->getNode('default/tele4G/ss4/wsdl');        
        return $ss4;        
    }
    
    /**
     * GA account
     * @return account id
     */    
    public function checkGA()
    {
        $accountId = Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT);
        return $accountId;        
    }
    
    
    /**
     * Adform tracking JS
     * @return 
     */    
    public function checkTrackingJS()
    {
        $track1 = Tele4G_Common_Block_Adform::ADFORM_TRACKING_CAMPAIGN_ID;
        $track2 = Tele4G_Common_Block_Adform::ADFORM_TRACKING_POINT_ID_ADDON;
        $track3 = Tele4G_Common_Block_Adform::ADFORM_TRACKING_POINT_ID_KASSA;
        $track4 = Tele4G_Common_Block_Adform::ADFORM_TRACKING_POINT_ID_KVITTO;
        
        $output = '';
        $output .= "ADFORM_TRACKING_CAMPAIGN_ID - $track1; ";
        $output .= "ADFORM_TRACKING_POINT_ID_ADDON - $track2; ";
        $output .= "ADFORM_TRACKING_POINT_ID_KASSA - $track3; ";
        $output .= "ADFORM_TRACKING_POINT_ID_KVITTO - $track4; ";
        
        return $output;
    }
    
    /**
     * Adform tracking JS
     * @return auriga status
     */    
    public function checkAuriga()
    {
        $aurigaMode = Mage::getStoreConfig('payment/tele4G_auriga/api_test');
        if($aurigaMode == 0){
            $aurigaMode = 'No';
            $aurigaUrl = Mage::getConfig()->getNode('default/payment/tele4G_auriga/cgi_url');
        } else{
            $aurigaMode = 'Yes';
            $aurigaUrl = Mage::getConfig()->getNode('default/payment/tele4G_auriga/cgi_url_test');
        }        
        return "Auriga test mode - $aurigaMode, Auriga url - $aurigaUrl ";        
    }
    
    /**
     * SMTP
     * @return smtp host
     */    
    public function checkSmtp()
    {
        $smtp = Mage::getStoreConfig('system/smtp/host');
        return $smtp;
    }
    
    /**
     * Store email addresses
     * @return (string) email
     */    
    public function checkStoreEmail()
    {
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        return $email;
    }
    
    /**
     * NAS
     * @return (string) nas
     */    
    public function checkNAS()
    {
        $nas = Mage::getConfig()->getNode('default/tele4G/ss4/wsdl');
        return $nas;
    }
    
    /* TO DO */
    /**
     * Global static resources path
     * @return 
     */    
    public function checkStaticPath()
    {
        return 'TO DO';
    }
       
    /**
     * Magento SOAP API
     * @return 
     */    
    public function checkSoapApi()
    {
        $user = Mage::getModel('api/user')->load(1);
        return serialize($user->getDAta());
    }
    
    
    /**
     * Magento Database
     * @return 
     */    
    public function checkDatabase()
    {
        $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
        $connection = $config->host.';'.$config->dbname.';'.$config->username.';';
        $connection .= $config->password;
        return $connection;
    }
    
    
    /**
     * check IP
     * @return (string) IP
     */    
    public function checkIp()
    {
       $ip=$_SERVER['REMOTE_ADDR'];
        return $ip;        
    }
}