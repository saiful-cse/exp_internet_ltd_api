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

$zone = $error = $date1 = $date2 = "";
if ($_SESSION['employee_id'] == '6606') {
  $zone = 'OsmanPt';
}else if($_SESSION['employee_id'] == '6607'){
  $zone = 'OsmanMp';
}

$database = new Database();
$db = $database->getConnection();
$client = new Client($db);

if (isset($_POST['submit'])) {
  $date1 = $_POST['date1'];
  $date2 = $_POST['date2'];
  $client->first_date = $_POST['date1'] . " 00:00:00";
  $client->last_date = $_POST['date2'] . " 23:59:59";
  $client->zone = $zone;
  $transactions = $client->transaction();
  $total_payment = $client->totalPayment();
}
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
          <p style="color: white;"></p>
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
            <a class="nav-link" href="registered.php">Registered</a>
            <a class="nav-link active" href="transaction.php">Transaction</a>
          </nav>
        </div>
      </div>
      <div class="col-sm"></div>
    </div>
    <!-- dashboard Card End -->

    <!-- 1st Card Start -->
    <div class="row">
      <div class="col-sm"></div>
      <div class="col-sm">
        <div class="card">
          <div class="card-body">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
              <div class="d-flex align-items-center">
                <div class="d-flex align-items-start">
                  <input type="date" name="date1" required value="<?php echo $date1; ?>">
                </div>
                <div class="d-flex align-items-end">
                  <input type="date" name="date2" required value="<?php echo $date2; ?>">
                </div>
              </div>
              <button type="submit" name="submit" class="form-control btn btn-outline-primary">View</button>
            </form>

            <table class="table caption-top table-striped">
              <thead>
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Details</th>
                  <th scope="col">TK</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (isset($transactions)) {
                  foreach ($transactions as $txn) { ?>
                    <tr>
                      <td><?php echo $txn['date']; ?></td>
                      <td><?php echo $txn['details']; ?></td>
                      <td><?php echo $txn['credit']; ?></td>
                    </tr>
                <?php  }
                }
                ?>
              </tbody>
            </table>
             <div class="d-flex justify-content-end">
              <?php echo "Total Payment: ".$total_payment." TK </br> 35% Commision: ".$total_payment*(35/100)." TK"; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm"></div>
    </div>

    <!-- 1st Card End -->
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>