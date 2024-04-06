<?php
    // Include the database connection file
    include_once "./include/config/dbConnect.php";

    // Fetch plans from the database
    $sqlQ = "SELECT * FROM ost_plans";
    $stmt = $db->prepare($sqlQ);
    $stmt->execute();
    $result = $stmt->get_result();

    // Get logged-in user ID
    $loggedInUserID = $thisclient->getId();
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
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&vault=true&intent=subscription&components=buttons"></script>

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
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <div class="card">
                                <div class="price">$<?php echo $row['price'].'/'.$row['interval'] ?></div>
                                <ul class="features">
                                    <li><?php echo $row['name'] ?></li>
                                    <li><?php echo $row['description'] ?></li>
                                </ul>
                                <!-- Use the label directly to act as a button -->
                                <input type="radio" id="plan_<?php echo $row['id'] ?>" name="plan" class="radio-button" value="<?php echo $row['id'] ?>">
                                <label for="plan_<?php echo $row['id'] ?>" class="radio-label">Select Now</label>
                            </div>
                            <?php
                        }
                    }
                ?>
            </td>
        </tr>
    </table>
    <hr>
    <p style="text-align: center;">
        <!-- Display status message -->
    <div id="paymentResponse" class="hidden"></div>

    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <!--<input type="submit" value="<?php /*echo __('Continue'); */?>"/>
        <input type="button" value="<?php /*echo __('Cancel'); */?>" onclick="javascript: window.location.href='index.php';"/>-->
    </p>
</form>
<script>
    paypal.Buttons({
        createSubscription: async (data, actions) => {
            setProcessing(true);

            // Get the selected plan ID
            let subscr_plan_id = document.querySelector('.radio-button').value;

            // Send request to the backend server to create subscription plan via PayPal API
            let postData = {request_type: 'create_plan', plan_id: subscr_plan_id};

            const PLAN_ID = await fetch("./include/paypal_checkout_init.php", {
                method: "POST",
                headers: {'Accept': 'application/json'},
                body: encodeFormData(postData)
            }).then((res) => {
                return res.json();
            }).then((result) => {
                setProcessing(false);
                if(result.status == 1){
                    return result.data.id;
                }else{
                    resultMessage(result.msg);
                    return false;
                }
            });

            // Creates the subscription
            return actions.subscription.create({
                'plan_id': PLAN_ID,
                'custom_id': '<?php echo $loggedInUserID; ?>'
            });
        },
        onApprove: (data, actions) => {
            setProcessing(true);

            // Send request to the backend server to validate subscription via PayPal API
            var postData = {
                request_type:'capture_subscr',
                order_id:data.orderID,
                subscription_id:data.subscriptionID,
                plan_id: document.querySelector('.radio-button').value
            };

            fetch('./include/paypal_checkout_init.php', {
                method: 'POST',
                headers: {'Accept': 'application/json'},
                body: encodeFormData(postData)
            })
            .then((response) => response.json())
            .then((result) => {
                if(result.status == 1) {
                    // Redirect the user to the status page
                    window.location.href = "payment-status.php?checkout_ref_id="+result.ref_id;
                } else {
                    resultMessage(result.msg);
                }
                setProcessing(false);
            })
            .catch(error => console.log(error));
        }
    }).render('#paypal-button-container');

    // Helper function to encode payload parameters
    const encodeFormData = (data) => {
        var form_data = new FormData();

        for ( var key in data ) {
            form_data.append(key, data[key]);
        }
        return form_data;
    }


    // Show a loader on payment form processing
    const setProcessing = (isProcessing) => {
        if (isProcessing) {
            document.querySelector(".overlay").classList.remove("hidden");
        } else {
            document.querySelector(".overlay").classList.add("hidden");
        }
    }

    // Display status message
    const resultMessage = (msg_txt) => {
        const messageContainer = document.querySelector("#paymentResponse");

        messageContainer.classList.remove("hidden");
        messageContainer.textContent = msg_txt;

        setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageContainer.textContent = "";
        }, 5000);
    }
</script>