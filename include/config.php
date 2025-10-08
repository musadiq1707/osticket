<?php
/*
 * PayPal and database configuration
 */

// PayPal configuration
define('PAYPAL_ID', '');
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production

define('PAYPAL_RETURN_URL', 'http://osticket.test/success.php');
define('PAYPAL_CANCEL_URL', 'http://osticket.test/cancel.php');
define('PAYPAL_NOTIFY_URL', 'http://osticket.test/ipn.php');
define('PAYPAL_CURRENCY', 'USD');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'osticket');

// Change not required
define('PAYPAL_URL', (PAYPAL_SANDBOX == true)?"https://www.sandbox.paypal.com/cgi-bin/webscr":"https://www.paypal.com/cgi-bin/webscr");
?>
