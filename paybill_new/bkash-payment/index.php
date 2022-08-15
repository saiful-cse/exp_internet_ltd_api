<?php
session_start();
date_default_timezone_set('Asia/Dhaka');

if (!isset($_SESSION['amount']) || !isset($_SESSION['client_id'])) {
    header('location: ../index.php');
    die();
    exit();
}

$request_token = bkash_Get_Token();


if (isset($request_token['id_token'])) {

    $_SESSION['token'] = $request_token['id_token'];

    $createApiResponse = createPaymentAPI();

    if (isset($createApiResponse['bkashURL'])) {

        header("Location: " . $createApiResponse['bkashURL']);
    } else {

        unset($_SESSION['client_id']);
        unset($_SESSION['amount']);
        unset($_SESSION['token']);

        header('location: ../index.php');
        die();
        exit();
    }
} else {

    unset($_SESSION['client_id']);
    unset($_SESSION['amount']);
    //redirecting to phone number serarch page.
    header('location: ../index.php');
    die();
    exit();
}

function bkash_Get_Token()
{

    include('./config.php');

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

function createPaymentAPI()
{
    include('./config.php');

    $amount = $_SESSION['amount'];
    $invoice = $_SESSION['client_id'] . 'T' . date("dmYHis");
    $_SESSION['invoice'] =  $invoice;

    $createpaybody = array(
        'mode' => '0011',
        'payerReference' => 'CID'.$_SESSION['client_id'],
        'amount' => $amount,
        'currency' => 'BDT',
        'merchantInvoiceNumber' => $invoice,
        'intent' => 'sale',
        'callbackURL' => "http://192.168.1.5/api/expert_internet_api/paybill_new/bkash-payment/callback.php"
    );
    $url = curl_init($createURL);

    $createpaybodyx = json_encode($createpaybody);

    $header = array(
        'Content-Type:application/json',
        'authorization:' . $_SESSION['token'],
        'x-app-key:' . $app_key
    );

    curl_setopt($url, CURLOPT_HTTPHEADER, $header);
    curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
    curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    //curl_setopt($url, CURLOPT_PROXY, $proxy);

    $resultdata = curl_exec($url);
    curl_close($url);

    // $_SESSION['creq'] = json_encode($createpaybody)."<br>";
    // $_SESSION['cres'] = $resultdata;
    
    return json_decode($resultdata, true);
}
