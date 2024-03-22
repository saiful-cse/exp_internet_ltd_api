<?php

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';


/*
 * Instance database and dashboard object
 */
$error = "";

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
        <strong>Patoli Road Kharulia Bazar Customer List</strong> <br>
    </div>
    <span id="error"><?php echo $error ?></span>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <label for="">Enter PPP Password</label> <br>
        <input type="password" name="password">
        <input type="submit" name="view" value="View">
    </form>
    <br>
    <table>

        <?php
        if (isset($_POST['view'])) {
            if (empty($_POST['password'])) {
                $error = "Enter correct password";
            } else if ($_POST['password'] != "8919161") {
                $error = "Enter correct password";
            } else { ?>

                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>PPP Name</th>
                    <th>Area</th>
                    <th>Reg Date</th>
                </tr>
                <?php

                $database = new Database();
                $db = $database->getConnection();

                $client = new Client($db);

                $stmt = $client->registered_client();

                foreach ($stmt as $c) { ?>
                    <tr>
                        
                        <td><?php echo $c['name']; ?></td>
                        <td><?php echo $c['phone']; ?></td>
                        <td><?php echo $c['ppp_name']; ?></td>
                        <td><?php echo $c['area']; ?></td>
                        <td><?php echo $c['reg_date']; ?></td>
                    
                    </tr>
        <?php }
            }
        }

        ?>

    </table>

</body>

</html>