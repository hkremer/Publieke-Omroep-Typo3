<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
class myRecipient {

    private $email;

    //private $naam;

    function __construct($email) {
        //$this->naam = $naam;
        $this->email = $email;
    }

}
*/
class tx_eonieuwsbrief_addFieldsToFlexForm {

    var $clientOverall;
    var $clientCampaign;
    var $wsdl = 'http://eo.dmd.omroep.nl/x/soap-v2/wsdl.php/overall';

    function addCampaigns($config) {


        $login = array(
            'username' => 'soapuser',
            'password' => '123456'
        );

        try {
            $this->clientOverall = new SoapClient($this->wsdl, array('proxy_host' => false, 'proxy_port' => false, 'classmap' => array('myLogin' => "DMdeliveryLoginType", 'myRecipient ' => "RecipientType")));
            $result = $this->clientOverall->getCampaigns($login);

            $optionList = array();
            $i = 0;
            foreach ($result->campaign as $camp) {
                if ($camp->is_active && $camp->has_soap_api) {

                    $optionList[$i] = array(0 => $camp->name, 1 => $camp->id);

                    $i++;
                }
            }
            $config['items'] = array_merge($config['items'], $optionList);
        } catch (Exception $e) {
            mail('beheerder@email.nl', 'eonieuwsbrief Selectfield Soap Error', $e->getMessage());
        }
        return $config;
    }

}

?>
