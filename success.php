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
    $payment_status = 'Completed';

    // Get plans info from the database
    $plans = get_table_data('ost_plans', 'id ="' . $item_number . '"');
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
                    <h1 class="card-title success">Your Payment has been Successful</h1>

                    <h4>Payment Information</h4>
                    <p><b>Reference Number:</b> <?php echo $payment_id; ?></p>
                    <p><b>Transaction ID:</b> <?php echo $txn_id; ?></p>
                    <p><b>Paid Amount:</b> $<?php echo $payment_gross; ?></p>
                    <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>

                    <h4>Product Information</h4>
                    <?php
                    foreach ($plans as $key => $row) {
                        ?>
                        <p><b>Name:</b> <?php echo $row->name; ?></p>
                        <p><b>Price:</b> $<?php echo $row->price; ?></p>
                        <p><b>Interval:</b> Per <?php echo $row->interval; ?></p>
                        <?php
                    }
                    ?>
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