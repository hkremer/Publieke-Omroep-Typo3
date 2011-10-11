<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$debug = false;
if ($debug) {
    error_log('tx_eonieuwsbrief_eid DEBUG ON', 0);
} else {
    error_log('tx_eonieuwsbrief_eid DEBUG OFF', 0);
}
$email = htmlspecialchars(trim($_POST['email']));
$campaign = htmlspecialchars(trim($_POST['campaign']));
$vink = htmlspecialchars(trim($_POST['vink']));
$group = "60"; // the Opt-in group (60)
$overallWsdl = 'http://eo.dmd.omroep.nl/x/soap-v3/wsdl.php';

if ($debug) {
    error_log('class.tx_eonieusbrief_eid.php: email=' . $email, 0);
    error_log('class.tx_eonieusbrief_eid.php: campaign=' . $campaign, 0);
    error_log('class.tx_eonieusbrief_eid.php: Wsdl=' . $overallWsdl, 0);
    error_log('class.tx_eonieusbrief_eid.php: vink=' . $vink, 0);
}

$login = array(
    'username' => 'soapuser',
    'password' => '123456'
);

$recipient = array(array('name' => 'email', 'value' => $email));

try {
    $client = new SoapClient($overallWsdl);
} catch (Exception $exception) {
    if ($debug)
        error_log($exception->getMessage(), 0);
    mail('beheer@email.nl', 'eonieuwsbrief SoapClient Soap Error', $exception->getMessage());
}


/*
 * Add a new recipient to a DMdelivery campaign.
 * Required credentials: 'insert' privilege for area 'Recipients'
 * @param login: DMdelivery login object.
 * @param campaignID: The database ID of the campaign to work with.
 * @param groupIDs: An array of groups (database IDs) to make the recipient a member of.
 * Provide at least one group. If this array *only* contains the ID of the Opt-in group (60),
 * *and* this is a non-existing recipient, the opt-in confirmation email will be sent to the recipient.
 * @param recipientData: An associative array (key: name of field, value: value of field) containing recipient data.
 * @param addDuplisToGroup: Whether or not to add this recipient to the groups, when the recipient is in the database already.
 * @param overwrite: In case the recipient already exists, whether or not to overwrite the known recipient data with the new data provided.
 * @returns: The database ID of the newly created recipient.
 */

try {
    $result = $client->addRecipient($login, $campaign, array($group), $recipient, 1, 0);
} catch (SoapFault $exception) {
    if ($debug)
        error_log($exception->getMessage(), 0);
    mail('beheer@email.nl', 'eonieuwsbrief addRecipient Soap Error', $exception->getMessage());
}

switch ($result->status) {
    case "OK": {
            if ($debug)
                error_log("OK", 0);
            break;
        }
    case "DUPLICATE": {
            if ($debug)
                error_log("DUPLICATE1", 0);
            if ($debug)
                error_log("ID:" . $result->id, 0);
            /*
             * Retrieve all campaigns an overall recipient is member of, and the groups they're member
             * of within those campaigns. Required credentials: access to area 'Overall recipients'
             * @param login: DMdelivery login object.
             * @param recipientID: The database ID of the overall recipient. Can be found via getRecipientsByMatch.
             * @returns: An array of campaigns (and groups) the recipient is member of.
             */
            try {
                $result2 = $client->getOverallRecipientCampaigns($login, $result->id);
            } catch (SoapFault $exception) {
                if ($debug)
                    error_log($exception->getMessage(), 0);
                mail('beheer@email.nl', 'eonieuwsbrief getOverallRecipientCampaigns Soap Error', $exception->getMessage());
            }
            $campaignexists = false;

            if ($debug)
                error_log("DUPLICATE2", 0);
            /*
             * <getOverallRecipientCampaigns_result>
              <recipientCampaign>
              <campaign_id>1</campaign_id>
              <group_ids>
              <int>60</int>
              </group_ids>
              </recipientCampaign>
              <recipientCampaign>
              <campaign_id>16</campaign_id>
              <group_ids>
              <int>60</int>
              </group_ids>
              </recipientCampaign>
              <recipientCampaign>
              <campaign_id>19</campaign_id>
              <group_ids>
              <int>50</int>
              <int>70</int>
              </group_ids>
              </recipientCampaign>
              <recipientCampaign>
              <campaign_id>42</campaign_id>
              <group_ids>
              <int>50</int>
              <int>70</int>
              </group_ids>
              </recipientCampaign>
              </getOverallRecipientCampaigns_result>
             */
            foreach ($result2->recipientCampaign as $value) {
                if ($debug)
                    error_log("valID:" . $value->campaign_id, 0);
                if ($value->campaign_id == $campaign) {
                    $campaignexists = true;
                    $resendconfirmmail = false;
                    if ($debug)
                        error_log("DUPLICATE3", 0);

                    if (is_array($value->group_ids->int)) {
                        foreach ($value->group_ids->int as $value2) {
                            if ($debug)
                                error_log("int:" . $value->group_ids->int, 0);
                            if ($value2 === 60) {
                                if ($debug)
                                    error_log("DUPLICATE4", 0);
                                $resendconfirmmail = true;
                                break;
                            }
                        }
                    } else if (is_int($value->group_ids->int)) {
                        if ($debug)
                            error_log("DUPLICATE5", 0);
                        if ($value->group_ids->int === 60) {
                            if ($debug)
                                error_log("DUPLICATE6", 0);
                            $resendconfirmmail = true;
                        }
                    }

                    break;
                }
            }

            /*
             * Add a new recipient to the overall DMdelivery database. Required credentials: 'insert' privilege for area 'Overall recipients'
             * @param login: DMdelivery login object.
             * @param campaignIDs: An array of overall campaigns (database IDs) to make the recipients a member of. Provide at least one campaign.
             * @param groupIDs: An array of groups (database IDs) to make the recipient a member of.
             * The groups here need to be present in all campaigns the recipient becomes a member of!
             * Provide at least one group. If this array *only* contains the ID of the Opt-in group (60), the
             * opt-in confirmation email will be sent to the recipient.
             * @param recipientData: An associative array (key: name of field, value: value of field) containing recipient data.
             * @param overwrite: In case the recipient already exists, whether or not to overwrite the
             * known recipient data with the new data provided. If set to 'false', and the recipient to be
             * added turns out to be duplicate, then the recipient will not be added to the given campaigns and groups!
             * @returns: The database ID of the newly created recipient.
             */

            if (!$campaignexists) {
                try {
                    $result3 = $client->addOverallRecipient($login, array($campaign), array($group), $recipient, 1);
                } catch (SoapFault $exception) {
                    if ($debug)
                        error_log($exception->getMessage(), 0);
                    mail('beheer@email.nl', 'eonieuwsbrief addOverallRecipient Soap Error', $exception->getMessage());
                }

                if ($debug)
                    error_log("missing", 0);
            } else if ($resendconfirmmail) {
                try {
                    //$result3 = $client->addOverallRecipient($login, array($campaign), array($group), $recipient, 1);
                } catch (SoapFault $exception) {
                    error_log($exception->getMessage(), 0);
                    mail('beheer@email.nl', 'eonieuwsbrief addOverallRecipient Soap Error', $exception->getMessage());
                }

                if ($debug)
                    error_log("resend", 0);
            } else {
                if ($debug)
                    error_log("nothing", 0);
            }

            break;
        }
    case "ERROR": {
            if ($debug)
                error_log('eonieuwsbrief ERROR Soap Error' . $exception->getMessage(), 0);
            mail('beheer@email.nl', 'eonieuwsbrief ERROR Soap Error', $exception->getMessage());
            break;
        }
    default: {
            if ($debug)
                error_log('eonieuwsbrief default', 0);
            break;
        }
}
?>
