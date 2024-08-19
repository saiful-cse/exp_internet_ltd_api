<?php
session_start();

include_once '../config/url_config.php';
include_once '../config/database.php';
include_once  '../objects/txn.php';

// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($_GET['jwt'], $key, array('HS256'));

    $database = new Database();
    $db = $database->getConnection();
    $txn = new Txn($db);
    $upstream_bill = $txn->upstream_bill();

    $_SESSION['jwt'] = $_GET['jwt'];
    $_SESSION['emp_id'] = $_GET['emp_id'];

} catch (\Throwable $th) {
    // tell the user access denied  & show error message
    echo json_encode(array(
        "status" => 401,
        "message" => "Access denied, login again\n" . $th->getMessage()
    ));
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EXPERT INTERNET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body style="margin: 15px;">
    <div style="overflow-x: auto;">
        <div class="d-flex justify-content-around">
            <h5>UPSTREAM BILL LIST</h5>
        </div>
        <div class="d-flex justify-content-around">
            <h5><a href="upstream_bill_add.php">Add Bill</a></h5>
        </div>
        <?php
        if (isset($_GET['message'])) {
            echo $_GET['message'];
        } else {
            echo "";
        }
        ?>

        <table class="table table-bordered border-primary">
            <tr>
                <th>Date</th>
                <th>Month</th>
                <th>Paid by</th>
                <th>Amount</th>
            </tr>

            <?php foreach ($upstream_bill as $bill) { ?>
                <tr>
                    <td><?php echo $bill['date']; ?></td>
                    <td><?php echo $bill['month']; ?></td>
                    <td><?php echo $bill['paid_by']; ?></td>
                    <td><?php echo $bill['amount']; ?></td>
                </tr>
            <?php } ?>

        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>