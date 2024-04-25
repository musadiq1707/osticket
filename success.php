<?php
    // Include configuration file
    include './include/config.php';

    // Include database connection file
    include_once './include/config/dbConnect.php';

    // If transaction data is available in the URL
    if(!empty($_GET['item_number']) && !empty($_GET['tx']) && !empty($_GET['amt']) && !empty($_GET['cc']) && !empty($_GET['st'])) {
        // Get transaction information from URL
        $user_id = $_GET['cm'];

        // Get ost_paypal_sub info from the database
        $payment = get_table_data('ost_paypal_sub', 'user_id="' . $user_id . '"');
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

                    <?php foreach ($payment as $row)  { ?>
                        <h4>Payment Information</h4>
                        <p><b>Reference Number:</b> <?php echo $row->payer_id; ?></p>
                        <p><b>Subscription ID:</b> <?php echo $row->subscr_id; ?></p>
                        <p><b>Paid Amount:</b> $<?php echo $row->mc_amount1; ?></p>
                        <p><b>Status:</b> <?php echo ucfirst($row->payer_status); ?></p>

                        <h4>Product Information</h4>
                        <p><b>Name:</b> <?php echo $row->item_name; ?></p>
                        <p><b>Price:</b> $<?php echo $row->amount1; ?></p>
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

