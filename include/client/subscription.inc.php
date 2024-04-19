<?php
// Include the database connection file
include_once "./include/config/dbConnect.php";

// Fetch plans from the database
$result = get_table_data('ost_plans');

// Get logged-in user ID
$user_id = $thisclient->getId();
?>
<style>
    .card {
        max-width: 219px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
    }

    .price {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .features {
        list-style-type: none;
        padding: 0;
    }

    .features li {
        margin-bottom: 10px;
    }

    /* Hide the default radio button circle */
    .radio-button + .radio-label::before {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid transparent;
        background-color: transparent;
        margin-right: 10px;
        vertical-align: middle;
    }

    .radio-button:checked + .radio-label::before {
        content: '\2713';
        text-align: center;
        line-height: 20px;
        background-color: #fff;
        color: #007bff;
        border-color: #007bff;
    }
</style>
<h1><?php echo __('Manage Your Membership'); ?></h1>
<p><?php echo __('Easily update your subscription details and access exclusive benefits.'); ?></p>

<div class="overlay hidden">
    <div class="overlay-content">
        <img src="images/loading.gif" alt="Processing..."/>
    </div>
</div>

<form action="subscription.php" method="post">
    <?php csrf_token(); ?>
    <table width="800" class="padded">
        <tr>
            <td colspan="2">
                <?php
                    foreach($result as $key => $row) {
                        ?>
                        <div class="card">
                            <div class="price">$<?php echo $row->price.'/'.$row->interval ?></div>
                            <ul class="features">
                                <li><?php echo $row->name ?></li>
                                <li><?php echo $row->description ?></li>
                            </ul>

                            <form action="<?php echo PAYPAL_URL; ?>" method="post">
                                <!-- Identify your business so that you can collect the payments. -->
                                <input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">

                                <!-- Specify a Subscribe button. -->
                                <input type="hidden" name="cmd" value="_xclick-subscriptions">

                                <!-- Identify the subscription. -->
                                <input type="hidden" name="item_name" value="<?php echo $row->name; ?>">
                                <input type="hidden" name="item_number" value="<?php echo $row->id; ?>">
                                <input type="hidden" name="custom" value="<?php echo $user_id; ?>">

                                <!-- Set the terms of the regular subscription. -->
                                <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">
                                <input type="hidden" name="a1" value="<?php echo $row->price; ?>">
                                <input type="hidden" name="p1" value="<?php echo $row->interval_count; ?>">
                                <input type="hidden" name="t1" value="M">

                                <input type="hidden" name="a3" value="<?php echo $row->price; ?>">
                                <input type="hidden" name="p3" value="<?php echo $row->interval_count; ?>">
                                <input type="hidden" name="t3" value="M">

                                <!-- Set recurring payments until canceled. -->
                                <input type="hidden" name="src" value="1">
                                <input type='hidden' name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">
                                <input type='hidden' name="return" value="<?php echo PAYPAL_RETURN_URL; ?>">
                                <input type='hidden' name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>">

                                <!-- Display the payment button. -->
                                <input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif">
                            </form>
                        </div>
                        <?php
                    }
                ?>
            </td>
        </tr>
    </table>
</form>