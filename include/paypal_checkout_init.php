<?php
    // Include the Paypal configuration file
    include_once 'config.php';

    // Include the database connection file
    include_once './include/config/dbConnect.php';

    // Include the PayPal API library
    require_once 'PaypalCheckout.class.php';

    $paypal = new PaypalCheckout;

    $response = array('status' => 0, 'msg' => 'Request Failed!');
    $api_error = '';

    if(!empty($_POST['request_type']) && $_POST['request_type'] == 'create_plan') {
        $plan_id = $_POST['plan_id'];

        // Fetch plan details from the database
        $sqlQ = "SELECT `name`,`price`,`interval`,`interval_count` FROM ost_plans WHERE id=?";
        $stmt = $db->prepare($sqlQ);
        $stmt->bind_param("i", $plan_id);
        $stmt->execute();
        $stmt->bind_result($planName, $planPrice, $planInterval, $intervalCount);
        $stmt->fetch();

        $plan_data = array(
            'name' => $planName,
            'price' => $planPrice,
            'interval' => $planInterval,
            'interval_count' => $intervalCount
        );

        // Create plan with PayPal API
        try {
            $subscr_plan = $paypal->createPlan($plan_data);
        } catch(Exception $e) {
            $api_error = $e->getMessage();
        }

        if(!empty($subscr_plan)) {
            $response = array(
                'status' => 1,
                'data' => $subscr_plan
            );
        } else {
            $response['msg'] = $api_error;
        }
    } elseif(!empty($_POST['request_type']) && $_POST['request_type'] == 'capture_subscr') {
        $order_id = $_POST['order_id'];
        $subscription_id = $_POST['subscription_id'];
        $db_plan_id = $_POST['plan_id'];

        // Fetch & validate subscription with PayPal API
        try {
            $subscr_data = $paypal->getSubscription($subscription_id);
        } catch(Exception $e) {
            $api_error = $e->getMessage();
        }

        if(!empty($subscr_data)) {
            $status = $subscr_data['status'];
            $subscr_id = $subscr_data['id'];
            $plan_id = $subscr_data['plan_id'];
            $custom_user_id = $subscr_data['custom_id'];

            $create_time = $subscr_data['create_time'];
            $dt = new DateTime($create_time);
            $created = $dt->format("Y-m-d H:i:s");

            $start_time = $subscr_data['start_time'];
            $dt = new DateTime($start_time);
            $valid_from = $dt->format("Y-m-d H:i:s");

            if(!empty($subscr_data['subscriber'])) {
                $subscriber = $subscr_data['subscriber'];
                $subscriber_email = $subscriber['email_address'];
                $subscriber_id = $subscriber['payer_id'];
                $given_name = trim($subscriber['name']['given_name']);
                $surname = trim($subscriber['name']['surname']);
                $subscriber_name = trim($given_name.' '.$surname);

                // Insert user details if not exists in the DB users table
                if(empty($custom_user_id)) {
                    $sqlQ = "INSERT INTO users (first_name,last_name,email,created) VALUES (?,?,?,NOW())";
                    $stmt = $db->prepare($sqlQ);
                    $stmt->bind_param("sss", $given_name, $surname, $subscriber_email);
                    $insertUser = $stmt->execute();

                    if($insertUser) {
                        $custom_user_id = $stmt->insert_id;
                    }
                }
            }

            if(!empty($subscr_data['billing_info'])) {
                $billing_info = $subscr_data['billing_info'];

                if(!empty($billing_info['outstanding_balance'])) {
                    $outstanding_balance_value = $billing_info['outstanding_balance']['value'];
                    $outstanding_balance_curreny = $billing_info['outstanding_balance']['currency_code'];
                }

                if(!empty($billing_info['last_payment'])) {
                    $last_payment_amount = $billing_info['last_payment']['amount']['value'];
                    $last_payment_curreny = $billing_info['last_payment']['amount']['currency_code'];
                }

                $next_billing_time = $billing_info['next_billing_time'];
                $dt = new DateTime($next_billing_time);
                $valid_to = $dt->format("Y-m-d H:i:s");
            }

            if(!empty($subscr_id) && $status == 'ACTIVE') {
                // Check if any subscription data exists with the same ID
                $sqlQ = "SELECT id FROM ost_user_subscriptions WHERE paypal_order_id = ?";
                $stmt = $db->prepare($sqlQ);
                $stmt->bind_param("s", $order_id);
                $stmt->execute();
                $stmt->bind_result($row_id);
                $stmt->fetch();

                $payment_id = 0;
                if(!empty($row_id)) {
                    $payment_id = $row_id;
                } else {
                    // Insert subscription data into the database
                    $sqlQ = "INSERT INTO ost_user_subscriptions (user_id,plan_id,paypal_order_id,paypal_plan_id,paypal_subscr_id,valid_from,valid_to,paid_amount,currency_code,subscriber_id,subscriber_name,subscriber_email,status,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
                    $stmt = $db->prepare($sqlQ);
                    $stmt->bind_param("iisssssdssssss", $custom_user_id, $db_plan_id, $order_id, $plan_id, $subscr_id, $valid_from, $valid_to, $last_payment_amount, $last_payment_curreny, $subscriber_id, $subscriber_name, $subscriber_email, $status, $created);
                    $insert = $stmt->execute();

                    if($insert) {
                        $user_subscription_id = $stmt->insert_id;

                        // Update subscription ID in users table
                        $sqlQ = "UPDATE ost_user_account SET subscription_id=? WHERE user_id=?";
                        $stmt = $db->prepare($sqlQ);
                        $stmt->bind_param("ii", $user_subscription_id, $custom_user_id);
                        $update = $stmt->execute();
                    }
                }

                if(!empty($user_subscription_id)) {
                    $ref_id_enc = base64_encode($order_id);
                    $response = array('status' => 1, 'msg' => 'Subscription created!', 'ref_id' => $ref_id_enc);
                }
            }
        } else {
            $response['msg'] = $api_error;
        }
    }
    echo json_encode($response);
?>