<?php
include_once './config/database.php';
include_once  './objects/device.php';

$database = new Database();
$db = $database->getConnection();

$device = new Device($db);
$stmt = $device->get_device_url();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $url = $row['api_base'] . "pppStatus.php";
    $login_ip = $row['login_ip'];
    $username = $row['username'];
    $password = $row['password'];
}
$postdata = array(
    'ppp_name' => $_GET['ppp_name'],
    'login_ip' => $login_ip,
    'username' => $username,
    'password' => $password
);


$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);


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

                    <nav class="nav justify-content-center">
                        <a class="nav-link" href="expired.php">Expired</a>
                        <a class="nav-link" href="registered.php">Registered</a>
                        <a class="nav-link active" href="#">PPPoE Details</a>
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
                        <h5 class="card-title"></h5>
                        <div>
                            <p class="card_phone">
                                Name: <?php echo $_GET['name']; ?> <br>
                                PPPoE: <?php echo $_GET['ppp_name']; ?> <br>
                                PPP Status: <?php echo $data['ppp_status']; ?> <br>
                                PPP Activity: <?php echo $data['ppp_activity']; ?> <br>
                                Router MAC: <?php echo $data['router_mac']; ?> <br>
                                Last Log Out: <?php echo $data['last_loged_out']; ?> <br>
                                Last Log In: <?php echo $data['last_log_in']; ?> <br>
                                Uptime: <?php echo $data['uptime']; ?> <br>
                                Download: <?php echo $data['download']; ?> <br>
                                Upload: <?php echo $data['upload']; ?> <br>
                                Connected IP: <?php echo $data['connected_ip']; ?> <br>
                            </p>
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