<?php
session_start();

if (!isset($_SESSION['mobile']) || !isset($_SESSION['client_id']) || !isset($_SESSION['amount'])) {
    header('location: index.php');
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
        Expert Internet Ltd.
    </title>

    <!-- bootstrap linked-->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <?php include('header.html') ?>
    <div class="main_content">
        <div class="container ">
            <div class="card paymentSelectorCard ">
                <div class="card-body">
                    <h6 style="text-align: center; color: #F16521; font-size: 20px;">Select Payment Method</h6>
                    <div class="payment_option mt-4">
                        <a href="#"><img src="img/nagodlogo.jpg" class="nagodlogo"></a>
                        <a href="./bkash-payment/"><img src="img/bkashlogo.jpg" class="bkashlogo"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.html') ?>
    <!--card end here-->
    <script src=" https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>