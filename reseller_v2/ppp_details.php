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
    <style>
        /* Loader styles */
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: none;
            /* Initially hidden */
            margin-left: -100px;
        }

        /* Loader animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Styles for the switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        /* Hide the default checkbox */
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Style the switch */
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        /* Style the switch when it's in the 'on' state */
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        /* When the switch is in the 'on' state, move the slider */
        input:checked+.toggle-slider {
            background-color: #EF6623;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(26px);
        }

        /* Text style for the switch label */
        .switch-label {
            margin-left: 10px;
            vertical-align: middle;
        }
    </style>
    <!-- add icon link -->
    <link rel="icon" href="https://expert-internet.net/logo/expert_internet.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
</head>

<body>

    <div class="container">
        <!-- dashboard Card Start -->
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm">
                <div class="header_card">

                    <nav class="nav justify-content-center">

                        <a class="nav-link active" href="#">PPPoE Details</a>
                        <a class="nav-link" href="onu_details.php?router_mac=<?php echo $data['router_mac']; ?>&onu_mac=<?php echo $_GET['onu_mac']; ?>&name=<?php echo $_GET['name']; ?>">ONU Details</a>
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
                                        <td>PPP Username</td>
                                        <td><?php echo $_GET['ppp_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Switch</td>
                                        <td>
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="myToggle" onclick="performAjaxCall('<?php echo $_GET['ppp_name']; ?>')" <?php echo ($data['ppp_status'] == 'Enable') ? 'checked' : '' ?>>
                                                <!-- Initial state set to 'checked' -->
                                                <span class="toggle-slider"></span>

                                            </label>

                                        </td>
                                        <td>
                                            <div class="loader" id="loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PPP Status</td>
                                        <?php echo ($data['ppp_status'] == 'Enable') ? '<td style="color: green;">Enable</td>' : '<td style="color: red;">Disable</td>' ?>

                                    </tr>
                                    <tr>
                                        <td>PPP Activiy</td>
                                        <?php echo ($data['ppp_activity'] == 'Online') ? '<td style="color: green;">Online</td>' : '<td style="color: red;">Offline</td>' ?>
                                    </tr>
                                    <tr>
                                        <td>Router MAC</td>
                                        <td><?php echo $data['router_mac']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Log in</td>
                                        <td><?php echo $data['last_log_in']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Log out</td>
                                        <td><?php echo $data['last_loged_out']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Uptime</td>
                                        <td><?php echo $data['uptime']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Download</td>
                                        <td><?php echo $data['download']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Upload</td>
                                        <td><?php echo $data['upload']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Connected IP</td>
                                        <?php if ($data['ppp_activity'] == 'Online') { ?>
                                            <td><?php echo $data['connected_ip']; ?> <a target="_blank" href="<?php echo "http://" . $data['connected_ip'] . ":8080" ?>"> Router Login</a></td>
                                        <?php   } else {
                                            echo "<td>" . $data['connected_ip'] . "</td>";
                                        } ?>
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

    <script type="text/javascript">
        // Function to handle the AJAX call on toggle switch click
        function performAjaxCall(ppp_name) {
            const myToggle = document.getElementById('myToggle');
            const loader = document.getElementById('loader');

            // Show loader while fetching data
            loader.style.display = 'block';

            var jsonData = {
                ppp_name: ppp_name,
                action_type: myToggle.checked ? 'enable' : 'disable'
                
            };

            $.ajax({
                url: "ppp_action.php",
                method: 'POST',
                data: jsonData,

                success: function(response) {
                    console.log('Toggle state:', myToggle.checked ? 'enabled' : 'disabled');
                    loader.style.display = 'none';
                    console.log(response);

                },
                error: function(error) {
                    loader.style.display = 'none';
                    console.log(response);
                }
            })
        }
    </script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>