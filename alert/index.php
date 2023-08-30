<?php

/* Example for adding a VPN user */

require('./routeros_api.class.php');

$API = new RouterosAPI();

$API->debug = true;

if ($API->connect('103.134.39.234', 'api', 'api@0011##&!')) {

    // $API->comm("/ppp/secret/add", array(
    //    "name"     => "user",
    //    "password" => "pass",
    //    "remote-address" => "172.16.1.10",
    //    "comment"  => "{new VPN user}",
    //    "service"  => "pptp",
    // ));

    // $API->write('/interface ethernet monitor radio');
    $API->write('/interface/monitor', false);
    $API->write('=interface=radio', false);
    $API->write('=once');
    $READ = $API->read(false);
    $ARRAY = $API->parseResponse($READ);
    //echo json_encode($ARRAY);
    print_r($ARRAY);
    $API->disconnect();

    //1/2-BKP-Link-Sfp-combo-1G-RADIO
}
