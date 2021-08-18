<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

$area = ['Select Area Name', 'MASTER PARA', 'MASTER BARI', 'GAT PARA', 'CHAIRMAN PARA', 'KHRULIA BAZAR', 'SIKDAR PARA',
'MONSI PARA', 'DENGA PARA', 'CAKMARKUL', 'KOLGOR BAZAR', 'MIAZI PARA', 'TECCIFUL STATION', 'FATEKARKUL', 'BONIK PARA STATION'];
echo json_encode($area);

