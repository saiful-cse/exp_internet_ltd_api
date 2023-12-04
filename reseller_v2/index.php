<?php
session_start();
date_default_timezone_set("Asia/Dhaka");

$error = $employee_id = $employee_id = $admin_pin = "";

if (isset($_POST['login'])) {
  $employee_id = $_POST['employee_id'];
  $admin_pin = $_POST['admin_pin'];

  if (!empty($employee_id && !empty($pin))) {
    $error = '<div class="alert alert-danger">Enter Correct Employee ID</div>';
  } else if (strlen($employee_id) != 4) {
    $error = '<div class="alert alert-danger">Enter Correct Employee ID</div>';
  } else if (empty($admin_pin)) {
    $error = '<div class="alert alert-danger">Enter Correct Pin</div>';
  } else if (strlen($admin_pin) != 4) {
    $error = '<div class="alert alert-danger">Enter Correct Pin</div>';
  } else {

    include_once './config/database.php';
    include_once  './objects/admin.php';

    $database = new Database();
    $db = $database->getConnection();

    $admin = new Admin($db);
    $admin->employee_id = $employee_id;
    $admin->pin = $admin_pin;
    $admin->details = $employee_id . " admin login successfully";

    $stmt =  $admin->login();
    $num = $stmt->rowCount();

    if ($num > 0) {
      $admin->login_record();

      //Session time is stored in a session variable
      $_SESSION['login_session_time'] = time() + 1200;
      $_SESSION['loged'] = 'loged';
      $_SESSION['employee_id'] = $employee_id;
      header('location: expired.php');
    } else {
      $error = '<div class="alert alert-danger">Wrong ID or Pin</div>';
    }
  }
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
  <div class="login_main_div">
    <div class="container">
      <!-- 1st Card Start -->
      <div class="row">
        <div class="col-sm"></div>
        <div class="col-sm ">
          <div class="card login_card" style="margin-top: auto;margin-bottom: auto;">
            <div class="card-body">
              <form action="index.php" method="post">
                <div class="input-group mb-3">
                  <input type="tel" placeholder="ID" name="employee_id" value="<?php echo $employee_id ?>" class="form-control" maxlength="4" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                  <input type="tel" placeholder="Pin" name="admin_pin" value="<?php echo $admin_pin ?>" class="form-control" maxlength="4" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                </div>
                <center>
                  <?php echo $error; ?>
                </center>
                <button type="submit" name="login" class="form-control btn btn-primary">Login</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-sm"></div>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>