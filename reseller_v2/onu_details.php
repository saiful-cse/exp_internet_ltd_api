<?php

$onu_mac = $olt_port = $onu_id = $onu_status = $drescrip = $distance = $last_login = $last_logout = $dreg_reason = $uptime = $power = "";

if ($_GET['router_mac'] != '---') {
    $olt_url = "https://kgnet.xyz/business_api/apivsol.php?auth=Djt875hgKikhSf77fsjk98&action=macstatus&mac=" . $_GET['router_mac'];
    $ch = curl_init($olt_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, true);

    if($data['status'] == 404){
        $olt_port = $onu_id = $onu_status = $drescrip = $distance = $last_login = $last_logout = $dreg_reason = $uptime = $power = "";
        $onu_mac = "Not Updated in database.";
    }

    $onu_mac = $data['onu_mac'];
    $olt_port = $data['olt_port'];
    $onu_id = $data['onu_id'];
    $onu_status = $data['onu_status'];
    $drescrip = $data['description'];
    $distance = $data['distance'];
    $last_login = $data['last_reg_time'];
    $last_logout = $data['last_dreg_time'];
    $dreg_reason = $data['dreg_reason'];
    $uptime = $data['uptime'];
    $power = $data['rx_power'];
}
//  else if ($_GET['onu_mac'] != '---') {


//     $olt_url = "https://kgnet.xyz/business_api/apivsol.php?auth=Djt875hgKikhSf77fsjk98&action=macstatus&mac=" . $_GET['router_mac'];
//     $ch = curl_init($olt_url);
//     curl_setopt($ch, CURLOPT_POST, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
//     $result = curl_exec($ch);
//     curl_close($ch);

//     $data = json_decode($result, true);
// }

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

                        <a class="nav-link active" href="#">ONU Details</a>

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
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Client Name</td>
                                        <td><?php echo $_GET['name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>ONU MAC</td>
                                        <td><?php echo $onu_mac ?></td>
                                    </tr>
                                    <tr>
                                        <td>OLT Port</td>
                                        <td><?php echo $olt_port ?></td>
                                    </tr>
                                    <tr>
                                        <td>ONU ID</td>
                                        <td><?php echo $onu_id ?></td>
                                    </tr>
                                    <tr>
                                        <td>ONU Status</td>
                                        <?php echo ($onu_status == 'Online') ? '<td style="color: green;">Online</td>' : '<td style="color: red;">Offline</td>' ?>
                                    </tr>
                                    <tr>
                                        <td>Description</td>
                                        <td><?php echo $drescrip; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Distance</td>
                                        <td><?php echo $distance; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Login Time</td>
                                        <td><?php echo $last_login ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Logout Time</td>
                                        <td><?php echo $last_logout ?></td>
                                    </tr>
                                    <tr>
                                        <td>Dereg. Reason</td>
                                        <td><?php echo $dreg_reason ?></td>
                                    </tr>
                                    <tr>
                                        <td>Uptime</td>
                                        <td><?php echo $uptime ?></td>
                                    </tr>
                                    <tr>
                                        <td>Power</td>
                                        <td><?php echo $power ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>