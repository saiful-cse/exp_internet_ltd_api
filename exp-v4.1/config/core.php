<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Dhaka');
 
// variables used for jwt
$key = "expert_internet";
$issued_at = time();
$expiration_time = $issued_at + (60 * 20); //in 1200 second or 20min
$issuer = "saiful";
?>