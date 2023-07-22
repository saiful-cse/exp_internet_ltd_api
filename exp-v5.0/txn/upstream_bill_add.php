<?php
session_start();

date_default_timezone_set("Asia/Dhaka");

include_once '../config/url_config.php';
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

$message = $amount = $month = $method = "";

if (isset($_POST['add'])) {

    $amount = $_POST['amount'];
    $month = $_POST['month'];
    $method = $_POST['method'];

    if (empty($amount)) {
        $message = '<div class="alert alert-warning" role="alert">Enter bill amount</div>';
    } else if ($month == '---') {
        $message = '<div class="alert alert-warning" role="alert">Select month of bill</div>';
    } else if ($method == '---') {
        $message = '<div class="alert alert-warning" role="alert">Select payment method</div>';
    } else {

        try {
            $decoded = JWT::decode($_SESSION['jwt'], $key, array('HS256'));

            include_once '../config/database.php';
            include_once  '../objects/txn.php';

            $database = new Database();
            $db = $database->getConnection();
            $txn = new Txn($db);

            $txn->month = $month;
            $txn->admin_id = $_SESSION['admin_id'];
            $txn->amount = $amount;
            $txn->method = $method;
            $txn->date = date("Y-m-d H:i:s");
            $txn->details = $month . " upstream bill";

            if ($txn->add_upstream_bill()) {

                $message = '<div class="alert alert-success" role="alert">Bill submited successfully</div>';
                
                unset($_SESSION['admin_id']);
                unset($_SESSION['jwt']);
                
                
            } else {
                $message = '<div class="alert alert-warning" role="alert">Something went wrong!</div>';
            }
            
        } catch (\Throwable $th) {
            // tell the user access denied  & show error message
            echo json_encode(array(
                "status" => 401,
                "message" => "Access denied, login again\n" . $th->getMessage()
            ));
        }
    }
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
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <?php echo $message; ?>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" value="<?php echo $amount ?>" name="amount" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Month of Bill</label>
            <select name="month" class="form-select">
                <option>---</option>
                <option>January, 2023</option>
                <option>February, 2023</option>
                <option>March, 2023</option>
                <option>April, 2023</option>
                <option>May, 2023</option>
                <option>June, 2023</option>
                <option>July, 2023</option>
                <option>August, 2023</option>
                <option>September, 2023</option>
                <option>October, 2023</option>
                <option>November, 2023</option>
                <option>December, 2023</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="method" class="form-select">
                <option>---</option>
                <option>Cash</option>
                <option>bKash</option>
                <option>Nogod</option>
                <option>Bank</option>
            </select>
        </div>
        <br>
        <div class="d-flex justify-content-center"><button type="submit" name="add" class="btn btn-primary">Submit</button></div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>