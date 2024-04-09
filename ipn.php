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
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = [];

foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

// Validate IPN message
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

// Post back to PayPal for validation
$paypalURL = PAYPAL_URL;
$ch = curl_init($paypalURL);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Connection: Close',
]);

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
if (!($res = curl_exec($ch))) {
    // HTTP error handling
    curl_close($ch);
    exit;
}

curl_close($ch);

// Extract user ID from IPN data
$user_id = isset($_POST['custom']) ? $_POST['custom'] : '';

// Process IPN response
if ($res == 'VERIFIED') {
    // IPN is verified, process the payment status
    // Retrieve transaction info from PayPal
    $item_number    = $_POST['item_number'];
    $txn_id         = $_POST['txn_id'];
    $payment_gross     = $_POST['mc_gross'];
    $currency_code     = $_POST['mc_currency'];
    $payment_status = $_POST['payment_status'];
    $payer_email = $_POST['payer_email'];

    // Handle payment status accordingly
    if ($payment_status == 'Completed') {
        // Check if transaction data exists with the same TXN ID
        $prevPayment = $db->query("SELECT payment_id FROM ost_payments WHERE txn_id = '".$txn_id."'");
        if($prevPayment->num_rows > 0) {
            exit();
        } else {
            // Insert transaction data into the database
            $insert = $db->query("INSERT INTO ost_payments(item_number,txn_id,payment_gross,currency_code,payment_status) VALUES('".$item_number."','".$txn_id."','".$payment_gross."','".$currency_code."','".$payment_status."')");
        }
    } else {
        if (!empty($user_id)) {
            $db->query("UPDATE ost_user_account SET subscription_id = null WHERE user_id = '".$user_id."'");
        }
    }
} else if ($res == 'INVALID') {
    if (!empty($user_id)) {
        $db->query("UPDATE ost_user_account SET subscription_id = null WHERE user_id = '".$user_id."'");
    }
} else {
    if (!empty($user_id)) {
        $db->query("UPDATE ost_user_account SET subscription_id = null WHERE user_id = '".$user_id."'");
    }
}