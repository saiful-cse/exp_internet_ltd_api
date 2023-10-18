<?php
date_default_timezone_set("Asia/Dhaka");
session_start();

if (!isset($_GET['mobile_no'])) {
    header('location: ../index.php');
    die();
    exit();
}

/*
 * include database and object files
 */
include_once './config/database.php';
include_once  './objects/client.php';

$database = new Database();
$db = $database->getConnection();

/*
     * Initialize object
     * */
$client = new Client($db);
$client->phone = $_GET['mobile_no'];
$details = $client->client_details();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        EXPERT INTERNET SOLUTION
    </title>
    <!-- add icon link -->
    <link rel="icon" href="https://expert-internet.net/logo/expert_internet.png" type="image/x-icon">

    <link href="https://fonts.maateen.me/bangla/font.css" rel="stylesheet">

    <!-- bootstrap linked-->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        .info_footer {
            background: #F16521;
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm">
            <?php include('header.html') ?>

            <div class="container">
                <?php
                if (!empty($details)) {

                    switch ($details['pkg_id']) {
                        case 'Govt5':
                            $pkg_price = 600;
                            break;
                        case 'Basic':
                            $pkg_price = 800;
                            break;
                        case 'Standard':
                            $pkg_price = 1000;
                            break;
                        case 'Professional':
                            $pkg_price = 1200;
                            break;

                        default:
                            $pkg_price = 1000;
                            break;
                    }

                    $current_date = new DateTime(date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . '+3 days')));
                    $expiredate = new DateTime($details['expire_date']);

                    $_SESSION['name'] = $details['name'];
                    $_SESSION['client_id'] = $details['id'];
                    $_SESSION['ppp_name'] = $details['ppp_name'];


                    if ($details['mode'] == 'Enable' && $details['registered'] == 1) {

                        if ($expiredate <= $current_date) {

                            $month = date_diff($current_date, $expiredate);
                            $totalmonth = (float) $month->format('%m') + 1;
                            $totalamount = $totalmonth * $pkg_price;

                            $_SESSION['amount'] = $totalamount;
                            $exp = new DateTime(date('Y-m-d H:i:s', strtotime($details['expire_date'] . '+' . $totalmonth . ' month')));
                            $_SESSION['expire_date'] = $exp->format('Y-m-d H:i:s');
                ?>

                            <div class="card">
                                <p>
                                    <?php echo "<b>Name: </b>" . $details['name']; ?> <br>
                                    <?php echo "<b>Phone: </b>" . $details['phone']; ?> <br>
                                    <?php echo "<b>Area: </b>" . $details['area']; ?> <br>
                                    <?php echo "<b>PPPoE: </b>" . $details['ppp_name']; ?> <br>
                                    <?php echo "<b>Expire Date: </b>" . date_format($expiredate, "d F Y"); ?> <br>
                                    <?php echo "<b>Month : </b>" . $totalmonth; ?> <br>
                                    <?php echo "<b>Package Name: </b>" . $details['pkg_id']; ?> <br>
                                    <?php echo "<b>Package Price: </b>" . $pkg_price . " TK (Monthly)"; ?> <br>
                                    <?php echo "<b>Payable amount: </b>" . $pkg_price . ' X ' . $totalmonth . ' = ' . $totalamount . " TK"; ?> <br>
                                </p>
                                <p class="text-center" style="color: gray; font-family: 'Bangla', sans-serif; font-size: 20px;">সব তথ্য সঠিক থাকলে কনফার্ম করুন</p>
                                <div class="input-btn">
                                    <a href="./bkash-payment/" class="form-control btn btn-secondary">Confirm</a>
                                </div>
                            </div>

                        <?php } else { ?>
                            <div class="card messageCard">
                                <div class="card-body">
                                    <img src="img/success.png" alt="" />
                                    <p style="color: green;">Congratulation</p>
                                    <h3 style="font-family: 'Bangla', sans-serif; font-size: 20px;"><?php echo $details['name'] . ", " . $details['area'] . ", " . $details['phone'] . "<br> আপনি " . $details['pkg_id'] . ", " . $details['speed'] . " প্যাকেজটি ব্যবহার করছেন, মাসিক বিল " . $pkg_price . " টাকা এবং " . date_format($expiredate, "d F Y") . " পর্যন্ত পরিশোধ করা আছে। <br> ধন্যবাদ"; ?></h3>

                                    <div class="input-btn mt-3">
                                        <a href="index.php" class="form-control btn btn-secondary">Exit</a>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else if ($details['mode'] == 'Disable' && $details['registered'] == 1) {

                        $nextExpDate = new DateTime(date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . '+1 month')));
                        $_SESSION['amount'] = $pkg_price;
                        $_SESSION['expire_date'] = $nextExpDate->format('Y-m-d H:i:s');
                        
                        ?>

                        <div class="card">
                            <p>
                                <?php echo "<b>Name: </b>" . $details['name']; ?> <br>
                                <?php echo "<b>Phone: </b>" . $details['phone']; ?> <br>
                                <?php echo "<b>Area: </b>" . $details['area']; ?> <br>
                                <?php echo "<b>PPPoE: </b>" . $details['ppp_name']; ?> <br>
                                <?php echo "<b>Next Expire Date: </b>" . date_format($nextExpDate, "d F Y") ?> <br>
                                <?php echo "<b>Package Name: </b>" . $details['pkg_id']; ?> <br>
                                <?php echo "<b>Package Price: </b>" . $pkg_price . " TK (Monthly)"; ?> <br>
                            </p>
                            <p class="text-center" style="color: gray; font-family: 'Bangla', sans-serif; font-size: 20px;">সব তথ্য সঠিক থাকলে কনফার্ম করুন</p>
                            <div class="input-btn">
                                <a href="./bkash-payment/" class="form-control btn btn-secondary">Confirm</a>
                            </div>
                        </div>
                    <?php } else if ($details['registered'] == 0) { ?>
                        <div class="card messageCard">
                            <div class="card-body">
                                <img src="img/error.png" alt="" />
                                <p style="color: red;">Error!!</p>
                                <h3 style="font-family: 'Bangla', sans-serif; font-size: 20px;">আপনার দেওয়া মোবাইল নাম্বারটি দিয়ে ইন্টারনেট প্যাকেজ রেজিস্ট্রশন করা হয়নি। হেল্প লাইনে যোগাযোগ করুন।</h3>
                                <div class="input-btn mt-3">
                                    <a href="index.php" class="form-control btn btn-secondary">Try Again</a>
                                </div>
                            </div>
                        </div>
                    <?php    }
                } else { ?>

                    <div class="card messageCard">
                        <div class="card-body">
                            <img src="img/error.png" alt="" />
                            <p style="color: red;">Error!!</p>
                            <h3 style="font-family: 'Bangla', sans-serif; font-size: 20px;">আপনার দেওয়া মোবাইল নাম্বারটি দিয়ে রেজিস্ট্রশন করা হয়নি। সঠিক মোবাইল নাম্বার দিয়ে আবার চেস্টা করুন।</h3>
                            <div class="input-btn mt-3">
                                <a href="index.php" class="form-control btn btn-secondary">Try Again</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <br>
            <br>
            <br>
            <?php include('footer.html') ?>
        </div>
        <div class="col-sm"></div>
    </div>


    <!--card end here-->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>