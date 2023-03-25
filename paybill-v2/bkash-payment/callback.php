<?php
session_start();
date_default_timezone_set("Asia/Dhaka");

$_SESSION['paymentID'] = $_GET['paymentID'];
$_SESSION['status'] = $_GET['status'];


if (
    !isset($_SESSION['paymentID']) || !isset($_SESSION['status']) || !isset($_SESSION['token']) ||
    !isset($_SESSION['amount']) || !isset($_SESSION['client_id'])
) {
    header('location: ../index.php');
    die();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Expert Internet
    </title>
    <!-- add icon link -->
    <link rel="icon" href="https://expert-internet.net/logo/expert_internet.png" type="image/x-icon">
    <link href="https://fonts.maateen.me/bangla/font.css" rel="stylesheet">

    <!-- bootstrap linked-->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>

    <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm">
            <?php include('header.html') ?>
            <br>
            <br>
            <div class="container">
                <?php
                switch ($_SESSION['status']) {
                    case 'cancel':
                        //View in design

                        unset($_SESSION['client_id']);
                        unset($_SESSION['amount']);
                        unset($_SESSION['token']);
                        unset($_SESSION['paymentID']);
                ?>
                        <div class="card messageCard">
                            <div class="card-body">
                                <img src="../img/error.png" alt="" />
                                <p style="color: red;">Canceled!!</p>
                                <h3>Payment has been canceled</h3>
                                <div class="input-btn mt-3">
                                    <a href="../index.php" class="form-control btn btn-secondary">Try Again</a>
                                </div>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'failure':
                        //View in design
                        //echo "Payment has been failed";
                        unset($_SESSION['client_id']);
                        unset($_SESSION['amount']);
                        unset($_SESSION['token']);
                        unset($_SESSION['paymentID']);

                    ?>
                        <div class="card messageCard">
                            <div class="card-body">
                                <img src="../img/error.png" alt="" />
                                <p style="color: red;">Failed!!</p>
                                <h3>Payment has been failed</h3>
                                <div class="input-btn mt-3">
                                    <a href="../index.php" class="form-control btn btn-secondary">Try Again</a>
                                </div>
                            </div>
                        </div>
                    <?php

                        break;

                    case 'success': ?>

                        <div id='loader' style='display: none; text-align: center;'>
                            <img src='../img/load.gif' width="100" height="100">
                            <p style="text-align: center;">Please wait....Your payment is being process</p>
                        </div>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
                        </script>

                        <script type="text/javascript">
                            const executeApiPromise = new Promise((resolve, reject) => {
                                $.ajax({
                                    type: "POST",
                                    url: "executepayment.php",
                                    success: function(data) {
                                        resolve(data)
                                    },
                                    error: function(error) {
                                        reject(`executeApiPromise rejected: ${error}`)
                                    }
                                })
                            });

                            const queryApiPromise = new Promise((resolve, reject) => {
                                $.ajax({
                                    type: "POST",
                                    url: "querypayment.php",
                                    success: function(data) {
                                        resolve(data)
                                    },
                                    error: function(error) {
                                        reject(`queryApiPromise rejected: ${error}`)
                                    }
                                })
                            });

                            const txnStoreApiPromise = (txnid, customerMsisdn) => {
                                return new Promise((resolve, reject) => {
                                    $.ajax({
                                        type: "POST",
                                        url: "txn_store.php",
                                        data: {
                                            "txnid": txnid,
                                            "customerMsisdn": customerMsisdn
                                        },
                                        success: function(data) {
                                            resolve(data)

                                        },
                                        error: function(error) {
                                            reject(`txnStoreApiPromise rejected: ${error}`)
                                        }
                                    })
                                });
                            }

                            (handelApiPromise = async () => {
                                $('#loader').show();

                                try {


                                    console.log("Calling Execute api....");
                                    const executePromiseData = await executeApiPromise;

                                    if (executePromiseData) {
                                        var executeObj = JSON.parse(executePromiseData);

                                        if (executeObj.transactionStatus === 'Completed') {
                                            try {
                                                var txnStorePromiseData = await txnStoreApiPromise(executeObj.trxID, executeObj.customerMsisdn);

                                                var txnStoreObj = JSON.parse(txnStorePromiseData);

                                                if (txnStoreObj.status === 200) {
                                                    console.log("Txn store success");
                                                    location.replace("../txnstatus.php?status=success");
                                                }

                                            } catch (error) {
                                                console.log(error);

                                            }

                                        } else {
                                            console.log(executeObj.statusMessage);
                                            location.replace("../txnstatus.php?status=" + executeObj.statusCode + ": " + executeObj.statusMessage);
                                        }


                                    } else {

                                        try {
                                            console.log("No response from execute api, calling Query api....");
                                            var queryPromiseData = await queryApiPromise;
                                            var queryobj = JSON.parse(queryPromiseData);

                                            console.log(queryPromiseData);
                                            if (queryobj.transactionStatus === 'Completed') {

                                                try {
                                                    var txnStorePromiseData = await txnStoreApiPromise(queryobj.trxID, queryobj.customerMsisdn);
                                                    var txnStoreObj = JSON.parse(txnStorePromiseData);

                                                    if (txnStoreObj.status === 200) {
                                                        console.log("Txn store success");
                                                        location.replace("../txnstatus.php?status=success");
                                                    }

                                                } catch (error) {
                                                    console.log(error);
                                                }

                                            } else {
                                                console.log(queryobj.statusMessage);
                                                location.replace("../txnstatus.php?status=" + queryobjs.statusCode + ": " + queryobj.statusMessage);
                                            }

                                        } catch (error) {
                                            console.log(error);
                                        }
                                    }



                                } catch (error) {
                                    console.log(error);
                                }
                                $('#loader').hide();
                            })()
                        </script>
                <?php
                }
                ?>

                <br>
                <br>
                <br>
                <?php include('footer.html') ?>
            </div>
        </div>
        <div class="col-sm"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

</body>

</html>