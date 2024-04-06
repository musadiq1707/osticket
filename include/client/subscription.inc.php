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

    .radio-button {
        display: none !important;
    }

    .radio-label {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .radio-label:hover {
        background-color: #0056b3;
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

<form action="subscription.php" method="post">
    <?php csrf_token(); ?>
    <table width="800" class="padded">
        <tr>
            <td colspan="2">
                <?php foreach ($packages as $package) { ?>
                    <div class="card">
                        <div class="price"><?php echo $package['price'] ?>/month</div>
                        <ul class="features">
                            <li><?php echo $package['name'] ?></li>
                            <li><?php echo $package['description'] ?></li>
                        </ul>
                        <!-- Use the label directly to act as a button -->
                        <input type="radio" id="package_<?php echo $package['id'] ?>" name="package" class="radio-button" value="<?php echo $package['id'] ?>">
                        <label for="package_<?php echo $package['id'] ?>" class="radio-label">Select Now</label>
                    </div>
                <?php } ?>
            </td>
        </tr>
    </table>
    <hr>
    <p style="text-align: center;">
        <input type="submit" value="<?php echo __('Continue'); ?>"/>
        <input type="button" value="<?php echo __('Cancel'); ?>" onclick="javascript: window.location.href='index.php';"/>
    </p>
</form>
