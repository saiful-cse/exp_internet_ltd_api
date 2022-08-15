<?php

/* Example for adding a VPN user */

require('../routeros_api.class.php');

$API = new RouterosAPI();

$API->debug = true;

if ($API->connect('103.134.39.242', 'admin', 'expnet@0011')) {

   // $API->comm("/ppp/secret/add", array(
   //    "name"     => "user",
   //    "password" => "pass",
   //    "remote-address" => "172.16.1.10",
   //    "comment"  => "{new VPN user}",
   //    "service"  => "pptp",
   // ));

    $API->write('/ppp/secret/getall');
    $READ = $API->read(false);
    $ARRAY = $API->parseResponse($READ);
    echo json_encode($ARRAY);
    //print_r($ARRAY);
    $API->disconnect();


}
