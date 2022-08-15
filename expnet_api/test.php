<?php

date_default_timezone_set("Asia/Dhaka");

$expire_date =  '2022-08-11 00:24:02';
echo $expire_date."\n";
  
// Add days to date and display it 
echo date('Y-m-d H:i:s', strtotime($expire_date. '1'. 'month')); 

?>