<?php

class Tele4G_Payment_Model_Aurigaerrors extends Mage_Core_Model_Abstract
{
    public function getErrorMessage($error_code)
    {
        $error_message = array(
                'E18' => array('message' => "Card Payment: Error upon Authorisation or 3-D Secure identification or no contact with the bank."),
                'E19' => array('message' => "Card Payment: Purchase denied at the bank (the card'svalidity period has expired), contact the bank."),
                'E20' => array('message' => "Card Payment: Purchase rejected at the bank, contact the bank."),
                'E21' => array('message' => "Country for card-issuing bank is not permitted."),
                'E22' => array('message' => "The risk assessment value for the transaction exceeds the permissible value."),
                'E23' => array('message' => "Card Payment: Card saved/inactivated at the card-issuing bank. E.g. lost, stolen, too many incorrect PIN entry attempts, etc."),
                'E24' => array('message' => "Error Request_type in Order Administration call."),
                'E25' => array('message' => "Amount too high: insufficient balance, card-issuing bank will not allow this amount on this card."),
                'E26' => array('message' => "Suspected fraud."),
                'E27' => array('message' => "Purchase amount (Amount) must be greater than zero."),
                'E28' => array('message' => "Denied due to too many payment attempts."),
                'E31' => array('message' => "Error in order, transaction already registered and paid. This Customer_refno is already registered on another transaction."),
                'E56' => array('message' => "Denied; the Merchant does not allow the re-use of an Orderno. (Customer_Refno). Merchant does not allow re-use of custrefnos."),
                'E82' => array('message' => "Card Payment: 3-D Secure identification denied due to timeout (for Verified by Visa or SecureCode). Occurs when the cardholder cannot identify themselves within approximately 5 minutes of the start of the transaction. Card Authorisation is not carried out."),

                'ss6' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'ss13' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'ss18' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'ss19' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'ss20' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'ss23' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'CanceledPayment' => array('message' => "Om du inte vill betala med ditt kontokort kan du välja att få en faktura eller betala med postförskott."),
                '*' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
                'BAD_MAC' => array('message' => "Det gick inte att genomföra betalningen med ditt kontokort."),
            );
        if (array_key_exists($error_code, $error_message)) {
            $message = $error_message[$error_code]['message'];
        } else {
            $message = "Det gick inte att genomföra betalningen med ditt kontokort.";
        }
        return $message;
    }
}
