<?php
session_start();
/*
 * include database and object files
 */
date_default_timezone_set("Asia/Dhaka");
include_once './config/database.php';
include_once  './objects/client.php';

if (!isset($_SESSION['loged']) && $_SESSION['login_session_time'] > time() && !isset($_SESSION['admin_id'])) {
  header('location: index.php');
  die();
  exit();
}

$zone = $error = "";
if ($_SESSION['employee_id'] == '6606') {
  $zone = 'OsmanPt';
}else if($_SESSION['employee_id'] == '6607'){
  $zone = 'OsmanMp';
}

$database = new Database();
$db = $database->getConnection();

$client = new Client($db);
$client_list = $client->registered_client($zone);
?>



<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css" />
  <title>EXPERT INTERNET</title>
  <!-- add icon link -->
  <link rel="icon" href="https://expert-internet.net/logo/expert_internet.png" type="image/x-icon">

</head>

<body>

  <div class="container">
    <!-- dashboard Card Start -->
    <div class="row">
      <div class="col-sm"></div>
      <div class="col-sm">
        <div class="header_card">
          <h6 class="text-center"><?php echo "Loged by " . $_SESSION['employee_id']; ?></h6>
          <p class="client_no"><?php echo "Total Client: " . $client->count_total_client($zone); ?></p>
          <div>
            <p class="client_no" style="display: inline;"><?php echo "Expired Client: " . $client->count_total_expired_client($zone); ?></p>
            <div style="display:inline;float: right;margin-right: 10px;">
              <button class="button logout_btn"><a href="logout.php">Log Out</a></button>
            </div>
          </div>
          <hr>
          <nav class="nav justify-content-center">
            <a class="nav-link" href="expired.php">Expired</a>
            <a class="nav-link active" href="registered.php">Registered</a>
            <a class="nav-link" href="transaction.php">Transaction</a>
          </nav>
        </div>
      </div>
      <div class="col-sm"></div>
    </div>
    <!-- dashboard Card End -->
    <?php
    foreach ($client_list as $item) { ?>
      <!-- 1st Card Start -->
      <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><a href="ppp_details.php?name=<?php echo $item['name'] ?>&ppp_name=<?php echo $item['ppp_name'] ?>&onu_mac=<?php echo $item['onu_mac'] ?>"><?php echo $item['name'] ?></a> </h5>

              <div>
                <p class="card_phone"><?php echo "Phone: " . $item['phone'] ?></p>
                <?php if ($item['mode'] == "Disable") { ?>
                  <p style="color: red;" class="card_status"><?php echo $item['mode'] ?></p>
                <?php } else if ($item['mode'] == "Enable") { ?>
                  <p style="color: green;" class="card_status"><?php echo $item['mode'] ?></p>
                <?php }  ?>
              </div>
              <div>
                <p class="card_ppp"><?php echo "PPP: " . $item['ppp_name'] ?></p>
                <!-- <p class="card_zone">Zone: Main</p> -->
                <p class="card_package"><?php echo "Package: " . $item['pkg_id'] ?></p>
              </div>
              <p class="card_area"><?php echo "Area: " . $item['area'] ?></p>
              <div class="bottom_card">
                <p class="card_date"><i class="fa fa-calendar" aria-hidden="true"></i>
                  <?php echo $item['expire_date'] ?></p>

                  <?php
                    $current_date = new DateTime(date('Y-m-d H:i:s'));
                    $expiredate = new DateTime($item['expire_date']);

                    if ($expiredate > $current_date) { ?>
                        <p class="card_payment">PAID</p>

                    <?php  } else { ?>
                      <p class="card_payment"><a target="_blank" href="https://expert-internet.net/paybill/info.php?mobile_no=<?php echo $item['phone'] ?>">PayBill</a></p>
                    <?php  } ?>
                <p class="card_time"><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo " " . $item['take_time'] ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm"></div>
      </div>
    <?php
    }
    ?>
    <!-- 1st Card End -->
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>