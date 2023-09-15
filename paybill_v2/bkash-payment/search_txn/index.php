<?php

$error = $txn_id = $pin = $response = "";

$request_token = bkash_Get_Token();

if (isset($request_token['id_token'])) {

    $_SESSION['token'] = $request_token['id_token'];

    if (isset($_POST['search'])) {
        $txn_id = $_POST['txn_id'];
        $pin = $_POST['pin'];
        if (empty($txn_id)) {
            $error = '<div class="alert alert-danger">Please enter bKash transaction ID</div>';
        } else {

            $response = 
            '<div class="card">
            <h5 class="card-title text-center">
                Result <br>
            </h5>
            <p>' . json_encode(searchTxn($txn_id)) .
                '</p>
            </div>';
        }
    }
} else {
    echo '<div class="alert alert-danger">Token generating error, try again</div>';
}


function bkash_Get_Token()
{

    include('../config.php');

    $post_token = array(
        'app_key' => $app_key,
        'app_secret' => $app_secret
    );

    $url = curl_init($tokenURL);
    $posttoken = json_encode($post_token);
    $header = array(
        'Content-Type:application/json',
        'password:' . $password,
        'username:' . $username
    );

    curl_setopt($url, CURLOPT_HTTPHEADER, $header);
    curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
    curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    //curl_setopt($url, CURLOPT_PROXY, $proxy);
    $resultdata = curl_exec($url);
    curl_close($url);
    //echo $resultdata;
    return json_decode($resultdata, true);
}


function searchTxn($txn_id)
{
    include('../config.php');

    $token = $_SESSION['token'];

    $url = curl_init($searchURL);

    $header = array(
        'Content-Type:application/json',
        'authorization:' . $token,
        'x-app-key:' . $app_key
    );

    $post_token = array(
        'trxID' => $txn_id
    );
    $post_data = json_encode($post_token);

    curl_setopt($url, CURLOPT_HTTPHEADER, $header);
    curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($url, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    //curl_setopt($url, CURLOPT_PROXY, $proxy);

    $resultdata = curl_exec($url);
    curl_close($url);
    return json_decode($resultdata, true);
}

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
    <link rel="stylesheet" href="./style.css">
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
                <img src="./img/explogo.png" alt="">
            </div>
            <div class="site_info">
                <h2>Expert Internet Ltd.</h2>
                <p>A Qualified internet service team</p>
            </div>
        </div>
    </div>
    <hr class="header_separator">
    <div class="main_content">
        <div class="container">
            <div class="card">
                <span><?php echo $error; ?></span>
                <form action="index.php" method="post">

                    <input style="text-align:center;" type="text" name="txn_id" maxlength="30" placeholder="bKash transactin ID" value="<?php echo $txn_id ?>" class="form-control input-btn mt-2"><br>

                    <div class="input-btn">
                        <button type="submit" name="search" class="form-control btn btn-secondary">Search</button>
                    </div>
                </form>
            </div>
            <br>
            <?php echo $response; ?>

        </div>
    </div>

    <footer>
        <div class="container">
            <div class="box1">
                <img src="img/phone_call.png" />
                <p>01975-559161</p>
            </div>
        </div>
    </footer>
    <!--card end here-->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>