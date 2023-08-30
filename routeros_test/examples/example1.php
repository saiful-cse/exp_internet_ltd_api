<?php

require('../routeros_api.class.php');

$API = new RouterosAPI();

$API->debug = true;

if ($API->connect('103.134.39.242', 'admin', 'expnet@0011')) {

   $API->write('/interface/print');

   $READ = $API->read(false);
   $ARRAY = $API->parseResponse($READ);

   var_dump($ARRAY);

   $API->disconnect();

}

?>
