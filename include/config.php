<?php
/* PayPal REST API configuration
 * You can generate API credentials from the PayPal developer panel.
 * See your keys here: https://developer.paypal.com/dashboard/
 */
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production
define('PAYPAL_SANDBOX_CLIENT_ID', 'AUFDSVCXYXG9oi0YI7C4a5ewUDrnQqzR86dT8IN91xWea8f8A-CO81vKjQomUFdX-49vv9WPTEqDSzNv');
define('PAYPAL_SANDBOX_CLIENT_SECRET', 'EMz0wGPx8kx8WfteYR6U4vMAS2dntH9WU4QxXSzenx7USX4kNFMcV5wkWfyilqTNivVNAenmdy_75AVP');
define('PAYPAL_PROD_CLIENT_ID', '');
define('PAYPAL_PROD_CLIENT_SECRET', '');

define('CURRENCY', 'USD');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'osticket');

// Start session
if(!session_id()){
    session_start();
}

?>