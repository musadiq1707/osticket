<?php
// Include configuration file
include './include/config.php';

// Include database connection file
include_once './include/config/dbConnect.php';

/*
 * Read POST data
 * reading posted data directly from $_POST causes serialization
 * issues with array data in POST.
 * Reading raw POST data from input stream instead.
 */
$paypalInfo = $_POST;
$result = curlPost(PAYPAL_URL, $paypalInfo);

//check whether the payment is verified
if($result == 'VERIFIED') {
    $txn_type = $paypalInfo['txn_type'];
    $subscr_id = $paypalInfo['subscr_id'];
    $last_name = $paypalInfo['last_name'];
    $residence_country = $paypalInfo['residence_country'];

    $mc_currency = $paypalInfo['mc_currency'];
    $item_name = $paypalInfo['item_name'];
    $business = $paypalInfo['business'];
    $amount1 = $paypalInfo['amount1'];
    $amount3 = $paypalInfo['amount3'];

    $recurring = $paypalInfo['recurring'];
    $address_street = $paypalInfo['address_street'];
    $verify_sign = $paypalInfo['verify_sign'];
    $payer_status = $paypalInfo['payer_status'];

    $test_ipn = $paypalInfo['test_ipn'];
    $payer_email = $paypalInfo['payer_email'];
    $custom = $paypalInfo['custom'];
    $address_status = $paypalInfo['address_status'];

    $first_name = $paypalInfo['first_name'];
    $receiver_email = $paypalInfo['receiver_email'];
    $address_country_code = $paypalInfo['address_country_code'];
    $payer_id = $paypalInfo['payer_id'];

    $address_city = $paypalInfo['address_city'];
    $reattempt = $paypalInfo['reattempt'];
    $item_number = $paypalInfo['item_number'];
    $address_state = $paypalInfo['address_state'];

    $subscr_date = $paypalInfo['subscr_date'];
    $address_zip = $paypalInfo['address_zip'];
    $charset = $paypalInfo['charset'];
    $notify_version = $paypalInfo['notify_version'];

    $period1 = $paypalInfo['period1'];
    $period3 = $paypalInfo['period3'];
    $address_country = $paypalInfo['address_country'];
    $mc_amount1 = $paypalInfo['mc_amount1'];
    $mc_amount3 = $paypalInfo['mc_amount3'];
    $address_name = $paypalInfo['address_name'];

    $ipn_track_id = $paypalInfo['ipn_track_id'];

    if($txn_type == 'subscr_payment') {
        $mc_gross = $paypalInfo['mc_gross'];
        $protection_eligibility = $paypalInfo['protection_eligibility'];
        $payment_date = $paypalInfo['payment_date'];
        $payment_status = $paypalInfo['payment_status'];
        $mc_fee = $paypalInfo['mc_fee'];
        $txn_id = $paypalInfo['txn_id'];
        $payment_type = $paypalInfo['payment_type'];
        $payment_fee = $paypalInfo['payment_fee'];
        $receiver_id = $paypalInfo['receiver_id'];
        $transaction_subject = $paypalInfo['transaction_subject'];
        $payment_gross = $paypalInfo['payment_gross'];
    }

    if($txn_type != 'subscr_payment') {
        $array_val = array("txn_type" => $txn_type, "subscr_id" => $subscr_id, "last_name" => $last_name, "residence_country" => $residence_country, "mc_currency" => $mc_currency, "item_name" => $item_name, "business" => $business, "amount1" => $amount1, "amount3" => $amount3, "recurring" => $recurring, "address_street" => $address_street, "verify_sign" => $verify_sign, "payer_status" => $payer_status, "test_ipn" => $test_ipn, "payer_email" => $payer_email, "custom" => $custom, "address_status" => $address_status, "first_name" => $first_name, "receiver_email" => $receiver_email, "address_country_code" => $address_country_code, "payer_id" => $payer_id, "address_city" => $address_city, "reattempt" => $reattempt, "item_number" => $item_number, "address_state" => $address_state, "subscr_date" => $subscr_date, "address_zip" => $address_zip, "charset" => $charset, "notify_version" => $notify_version, "period1" => $period1, "period3" => $period3, "address_country" => $address_country, "mc_amount1" => $mc_amount1, "mc_amount3" => $mc_amount3, "address_name" => $address_name, "ipn_track_id" => $ipn_track_id, "user_id" => $custom);
        $insert_info = insert_table_data($array_val, 'ost_paypal_sub');
        $subscribtionId = last_id();
    }

    if($txn_type == 'subscr_payment') {
        $array_val = array("mc_gross" => $mc_gross, "protection_eligibility" => $protection_eligibility, "address_status" => $address_status, "payer_id" => $payer_id, "address_street" => $address_street, "payment_date" => $payment_date, "payment_status" => $payment_status, "charset" => $charset, "address_zip" => $address_zip, "first_name" => $first_name, "mc_fee" => $mc_fee, "address_country_code" => $address_country_code, "address_name" => $address_name, "notify_version" => $notify_version, "subscr_id" => $subscr_id, "custom" => $custom, "payer_status" => $payer_status, "business" => $business, "address_country" => $address_country, "address_city" => $address_city, "verify_sign" => $verify_sign, "payer_email" => $payer_email, "txn_id" => $txn_id, "payment_type" => $payment_type, "last_name" => $last_name, "address_state" => $address_state, "receiver_email" => $receiver_email, "payment_fee" => $payment_fee, "receiver_id" => $receiver_id, "txn_type" => $txn_type, "item_name" => $item_name, "mc_currency" => $mc_currency, "item_number" => $item_number, "residence_country" => $residence_country, "transaction_subject" => $transaction_subject, "payment_gross" => $payment_gross, "ipn_track_id" => $ipn_track_id, "user_id" => $custom);
        $insert_info = insert_table_data($array_val, 'ost_payment');
        $paymentId = last_id();
    }

    if($subscribtionId != '') {
        $paypal_Id = $subscribtionId;
    } else {
        $paypal_Id = $paymentId;
    }

    if($txn_type == 'subscr_cancel') {
        $columns = "cancellation_status = '1', subscription_id = 'null'";
        update_table_data('ost_user_account', $columns, 'user_id="' . $custom . '"');
    } elseif ($txn_type == 'subscr_eot') {
        $columns = "cancellation_status = '2', subscription_id = 'null'";
        update_table_data('ost_user_account', $columns, 'user_id="' . $custom . '"');
    } else {
        $columns = "subscription_id = '" . $item_number . "', paypal_id = '" . $paypal_Id . "'";
        update_table_data('ost_user_account', $columns, 'user_id="' . $custom . '"');
    }
}

function curlPost($paypalURL, $paypalReturnArr)
{
    $req = 'cmd=_notify-validate';
    foreach($paypalReturnArr as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paypalURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}