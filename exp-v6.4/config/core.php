<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Dhaka');
 
// variables used for jwt
$key = "expertinternetltd";
$issued_at = time();
$expiration_time = $issued_at + (60 * 30); // valid for 30 min
$issuer = "saiful";
?>