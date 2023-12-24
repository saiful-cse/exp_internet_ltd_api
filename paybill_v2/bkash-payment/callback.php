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
        EXPERT INTERNET SOLUTION
    </title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
    <link href="https://fonts.maateen.me/bangla/font.css" rel="stylesheet">
    <!-- bootstrap linked-->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm">

            <div class="header">
                <div class="d-flex justify-content-center mt-2">
                    <img style="width: 350px; height: 50px;" src="../img/lo.png" alt="">
                </div>
            </div>
            <hr class="header_separator">

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
                            <p style="text-align: center, font-family: 'Bangla', sans-serif; font-size: 17px;">আপনার পেমেন্ট প্রসেস করা হচ্ছে, একটু অপেক্ষা করুন...</p>
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
            </div>

            <br>
            <br>
            <br>
            <p style="text-align: center; color:  gray; font-size: 10px;">Internet Service Provider of</p>
            <img src="../img/bayicon.png" alt="" style="width:50px;height:28px; display: block; margin-left: auto; margin-right: auto;">
            <p style="text-align: center; color: green; font-size: 20px;"><strong>BAY COMMUNICATION</strong></p> <br>
            <p style="text-align: center; color: black; font-size: 15px;">HELP LINE </br><strong> 01975-559161 </strong> (9AM to
                6PM)</p>

        </div>
        <div class="col-sm"></div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>