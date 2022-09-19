<?php
session_start();

if (!isset($_GET['status'])) {
  $status = "wrong";
} else {
  $status = $_GET['status'];
}

unset($_SESSION['client_id']);
unset($_SESSION['amount']);
unset($_SESSION['token']);
unset($_SESSION['paymentID']);
unset($_SESSION['expire_date']);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>
    Expert Internet Ltd.
  </title>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
  </script>

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
  <div class="header">
    <div class="d-flex justify-content-center mt-2">
      <div class="site_logo">
        <img src="img/explogo.png" alt="">
      </div>
      <div class="site_info">
        <h2>Expert Internet Ltd.</h2>
        <p>A Qualified Internet Service Team</p>
      </div>
    </div>
  </div>
  <hr class="header_separator">

  <div class="main_content">
    <div class="container">

      <?php

      if ($status === 'success') { ?>
        <script type="text/javascript">
          $.ajax({
            type: "POST",
            url: "http://mt.baycombd.com/expnet_api/pppAction.php",
            dataType: 'json',
            contentType: 'application/json',
            data: {
              "ppp_name": '<?php echo $_SESSION['ppp_name']; ?>',
              "action_type": 'enable'
            }
          })
        </script>
        <div class="card messageCard">
          <div class="card-body">
            <img src="img/success.png" alt="" />
            <p style="color: green;">Success</p>
            <h3>Payment has been completed</h3>
            <div class="input-btn mt-3">
              <a href="index.php" class="form-control btn btn-secondary">Exit</a>
            </div>
          </div>
        </div>
      <?php } else if ($status === 'wrong') {  ?>
        <div class="card messageCard">
          <div class="card-body">
            <img src="img/error.png" alt="" />
            <p style="color: red;">Error!!</p>
            <h3>Something went wrong!!</h3>
            <div class="input-btn mt-3">
              <a href="index.php" class="form-control btn btn-secondary">Try again</a>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <div class="card messageCard">
          <div class="card-body">
            <img src="img/error.png" alt="" />
            <p style="color: red;">Error!!</p>
            <h3> <?php echo $status; ?></h3>
            <div class="input-btn mt-3">
              <a href="index.php" class="form-control btn btn-secondary">Try again</a>
            </div>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>
  <footer>
    <div class="container">
      <div class="box1">
        <img src="img/phone_call.png">
        <p>01975-559161</p>
      </div>
    </div>

  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
  </script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
  </script>
</body>

</html>