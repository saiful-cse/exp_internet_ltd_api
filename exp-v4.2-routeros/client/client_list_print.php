<?php

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';


/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$client = new Client($db);
$stmt = $client->allClient();

?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        h3 {
            text-align: center;
        }

        #company_header {
            text-align: center;
        }

        #total {
            text-align-last: right;
            margin-right: 30px;
        }

        #error {
            color: #FF0000;
        }
    </style>
</head>

<body>

    <div id="company_header">
        <strong>Expert Internet Ltd.</strong> <br>
    </div>

    <table>
        <tr>
            <th>Client ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Area</th>
            <th>PPPoE Username</th>
            <th>PPPoE Password</th>
            <th>Fee</th>
            <th>Reg. Date</th>
            <th>Active Date</th>
            <th>Inactive Date</th>
        </tr>

        <?php foreach ($stmt as $client) { ?>
                <tr>
                    <td><?php echo $client['id']; ?></td>
                    <td><?php echo $client['name']; ?></td>
                    <td><?php echo $client['phone']; ?></td>
                    <td><?php echo $client['address']; ?></td>
                    <td><?php echo $client['area']; ?></td>
                    <td><?php echo $client['username']; ?></td>
                    <td><?php echo $client['password']; ?></td>
                    <td><?php echo $client['fee']; ?></td>
                    <td><?php echo $client['reg_date']; ?></td>
                    <td><?php echo $client['active_date']; ?></td>
                    <td><?php echo $client['inactive_date']; ?></td>
                </tr>
        <?php } ?>

    </table>

</body>

</html>