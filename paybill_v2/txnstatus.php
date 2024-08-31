<?php
session_start();

if (!isset($_SESSION['ppp_name'])) {
    header('location: index.php');
    die();
    exit();
}

if (!isset($_GET['status'])) {
    $status = "wrong";
} else {
    $status = $_GET['status'];
}
unset($_SESSION['token']);

function ppp_enable()
{
    include_once './config/database.php';
    include_once './objects/device.php';

    $database = new Database();
    $db = $database->getConnection();

    $device = new Device($db);
    $stmt = $device->get_device_url();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $url = $row['api_base'] . "pppAction.php";
        $login_ip = $row['login_ip'];
        $username = $row['username'];
        $password = $row['password'];
    }
    $postdata = array(
        'ppp_name' => $_SESSION['ppp_name'],
        'action_type' => 'enable',
        'login_ip' => $login_ip,
        'username' => $username,
        'password' => $password
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
    curl_exec($ch);
    curl_close($ch);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        BAY COMMUNICATION
    </title>
    <!-- add icon link -->
    <link rel="icon" href="https://expert-internet.net/logo/expert_internet.png" type="image/x-icon">
    <link href="https://fonts.maateen.me/bangla/font.css" rel="stylesheet">

    <!-- bootstrap linked-->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>

    <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm">
            <?php include('header.html') ?>
            <br>
            <div class="container">
                <?php

                if ($status === 'success') {
                    ppp_enable();
                    unset($_SESSION['ppp_name']);
                ?>
                    <div class="card messageCard">
                        <div class="card-body">
                            <img src="img/success.png" alt="" />
                            <p style="color: green; font-size: 15px;">
                                <strong> Payment has been completed</strong>
                            </p>
                            <p style="font-size: 12px;">
                                <?php echo "Name: " . $_SESSION['name']; ?> <br>
                                <?php echo "Reg. Phone: " . $_SESSION['phone']; ?> <br>
                                <?php echo "Sender: " . $_SESSION['customerMsisdn']; ?> <br>
                                <?php echo "Receiver: 018XXXXXXX44"; ?> <br>
                                <?php echo "bKash Txn Id: " . $_SESSION['trxID']; ?> <br>
                                <?php echo "Time: " . $_SESSION['completedTime']; ?> <br>
                                <?php echo "Amount: " . $_SESSION['amount'] . " TK"; ?>
                            </p>

                            <h3 style="font-family: 'Bangla', sans-serif; font-size: 12px; color:gray">আপনার ওয়াইফাই কানেকশনটি চালু করা হয়েছে, <br>লিংক দিয়ে বিল পেমেন্ট করার জন্য ধন্যবাদ</h3>

                            <div class="input-btn mt-3">
                                <a href="index.php" class="form-control btn btn-secondary">Exit</a>
                            </div>
                        </div>
                    </div>
                <?php
                } else if ($status === 'wrong') {
                ?>
                    <div class="card messageCard">
                        <div class="card-body">
                            <img src="img/error.png" alt="" />
                            <br> <br>
                            <h3>Something went wrong!!</h3>
                            <div class="input-btn mt-3">
                                <a href="index.php" class="form-control btn btn-secondary">Try again</a>
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="card messageCard">
                        <div class="card-body">

                            <img src="img/error.png" alt="" />
                            <br> <br>
                            <h3> <?php echo $status; ?></h3>
                            <div class="input-btn mt-3">
                                <a href="index.php" class="form-control btn btn-secondary">Try again</a>
                            </div>
                        </div>
                    </div>
                <?php
                } ?>

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