<?php
// Include configuration file
include './include/config.php';

// Include database connection file
include_once './include/config/dbConnect.php';

// If transaction data is available in the URL
if(!empty($_GET['item_number']) && !empty($_GET['tx']) && !empty($_GET['amt']) && !empty($_GET['cc']) && !empty($_GET['st'])) {
    // Get transaction information from URL
    $user_id = $_GET['cm'];
    $item_number = $_GET['item_number'];
    $txn_id = $_GET['tx'];
    $payment_gross = $_GET['amt'];
    $currency_code = $_GET['cc'];
    $payment_status = $_GET['st'];

    // Get product info from the database
    $productResult = $db->query("SELECT * FROM ost_plans WHERE id = ".$item_number);
    $productRow = $productResult->fetch_assoc();

    // Check if transaction data exists with the same TXN ID.
    $prevPaymentResult = $db->query("SELECT * FROM ost_payments WHERE txn_id = '".$txn_id."'");

    if($prevPaymentResult->num_rows > 0) {
        $paymentRow = $prevPaymentResult->fetch_assoc();
        $payment_id = $paymentRow['payment_id'];
        $payment_gross = $paymentRow['payment_gross'];
        $payment_status = $paymentRow['payment_status'];
    } else {
        // Insert tansaction data into the database
        $insert = $db->query("INSERT INTO ost_payments(item_number,txn_id,payment_gross,currency_code,payment_status) VALUES('".$item_number."','".$txn_id."','".$payment_gross."','".$currency_code."','".$payment_status."')");
        $payment_id = $db->insert_id;
    }

    // Update subscription ID in users table
    $sqlQ = "UPDATE ost_user_account SET subscription_id=? WHERE user_id=?";
    $stmt = $db->prepare($sqlQ);
    $stmt->bind_param("ii", $item_number, $user_id);
    $update = $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        .status-card {
            margin-top: 50px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card status-card">
                <div class="card-body">
                    <?php if (!empty($payment_id)) { ?>
                        <h1 class="card-title success">Your Payment has been Successful</h1>

                        <h4>Payment Information</h4>
                        <p><b>Reference Number:</b> <?php echo $payment_id; ?></p>
                        <p><b>Transaction ID:</b> <?php echo $txn_id; ?></p>
                        <p><b>Paid Amount:</b> $<?php echo $payment_gross; ?></p>
                        <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>

                        <h4>Product Information</h4>
                        <p><b>Name:</b> <?php echo $productRow['name']; ?></p>
                        <p><b>Price:</b> $<?php echo $productRow['price']; ?></p>
                    <?php } else { ?>
                        <h1 class="card-title error">Your Payment has Failed</h1>
                    <?php } ?>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

