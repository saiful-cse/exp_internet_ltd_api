<?php
session_start();

if (!isset($_SESSION['loged']) && $_SESSION['login_session_time'] > time()) {
    header('location: index.php');
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

$client = new Client($db);
$client_list = $client->client_list();

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Expert Internet Ltd.
    </title>

    <style>
        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>

<body>
    <?php include('header.html') ?>

    <h4 style="text-align: center;">PM Khali Customer List</h4> 
    <h5 style="text-align: right; margin-right: 50px;"><a href="logout.php">Log Out</a></h5>

    <div style="overflow-x:auto;">
        <table id="customers">
            <tr>
                <th>Name</th>
                <th>Area</th>
                <th>Username</th>
                <th>Phone</th>
                <th>Expire Date</th>
                <th>Mode</th>
                <th>Payment</th>
            </tr>

            <?php
            foreach ($client_list as $item) { ?>
                <tr>
                    <td><?php echo $item['name'] ?></td>
                    <td><?php echo $item['area'] ?></td>
                    <td><?php echo $item['ppp_name'] ?></td>
                    <td><?php echo $item['phone'] ?></td>
                    <td><?php echo $item['expire_date'] ?></td>
                    <?php if ($item['mode'] == "Disable") { ?>
                        <td style="text-align: center; color: #C21010;">Disable</td>
                    <?php } else if ($item['mode'] == "Enable") { ?>
                        <td style="text-align: center; color: green;">Enable</td>
                    <?php }  ?>
                    <?php
                    $current_date = new DateTime(date('Y-m-d H:i:s'));
                    $expiredate = new DateTime($item['expire_date']);

                    if ($expiredate > $current_date) { ?>
                        <td><b style="text-align: center; color: blue;">Paid</b></a></td>

                    <?php  } else { ?>
                        <td><a target="_blank" href="https://expert-internet.net/paybill/reseller_info.php?mobile_no=<?php echo $item['phone'] ?>">Pay Bill</a></td>
                    <?php  } ?>
                </tr>
            <?php } ?>

        </table>
    </div>
    <br> <br>

    <!--card end here-->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>