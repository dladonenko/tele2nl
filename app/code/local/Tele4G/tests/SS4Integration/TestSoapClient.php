<?php
class TestSoapClient{
     public function sendChallengeSms($number){
         $reuslt = (object)array('sendChallengeSmsResponse' => 
                                                    (object)array('responseStatus' => 
                                                                            (object)array('status' => 'OK','errorCode' => 0)));
         $reuslt = (object)$reuslt;
         return $reuslt;
     }
     
     
     public function validateSmsCode($params){
         $reuslt = (object)array('validateSmsCodeResponse' => (object)array('responseStatus' => (object)array('status' => 'OK','errorCode' => 0),
                                                                                                              'result' => 'SMS_VALIDATION_CODE_OK'
                                                                                                             ));
         return $reuslt;
         
     }
     
     
     public function getActivationTypeForExistingNumber($params){
         $reuslt = (object)array('activationType' => (object)array('responseStatus' => (object)array('status' => 'OK','errorCode' => 0),
                                                                  'activationType' => 'PORT'
                                                                 ));
         return $reuslt;
         
     }
     
     public function getStockLevel($params){ 
         $reuslt = (object)array('stockLevels' => (object)array('responseStatus' => (object)array('status' => 'OK','errorCode' => 0,'errorName' => 'ARTICLE_NOT_FOUND_IN_MAXIMO')));
         return $reuslt;
         
     }
     
     public function getAvailablePhoneNumbers($params){
         $reuslt = (object)array('phoneNumbers' => (object)array('responseStatus' => (object)array('status' => 'OK','errorCode' => 0),
                                                                  'phoneNumber' => array(
                                                                                        (object)array('number' => '0707202043'),
                                                                                        (object)array('number' => '0707202043'),
                                                                                        (object)array('number' => '0707202043'),
                                                                                        (object)array('number' => '0707202043'),
                                                                                        (object)array('number' => '0707202043'))
                                                                                   ));
         return $reuslt;
         
     }     
     
     public function getIndividual($params){ 
         $reuslt = (object)array('individualResponse' => (object)array(
             'responseStatus' => (object)array('status' => 'OK','errorCode' => 0,'errorName' => 'ARTICLE_NOT_FOUND_IN_MAXIMO'),
             'individual' => (object)array(
                                            'identificationNumber' => '194211016151',
                                            'contactInfo' => '',
                                            'address' =>(object)array('streetAddress' => 'PL 11155 TEGELBRUKSVILLAN','city' => 'Ã–REBRO','postalCode'=>'70233') ),
                                            'firstName' => 'SÃren Anders',
                                            'lastName' => 'Knutsson'
             ));
         return $reuslt;
         
     }
     
     
     
     public function __getLastRequest(){
         return ;
     }
     
     
     public function __getLastResponse(){
         return ;
     }
}