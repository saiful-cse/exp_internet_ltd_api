<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

$area= ['Select Area Name', 'DOKKIN PATOLI', 'MASTER PARA', 'MASTER BARI', 'GAT PARA', 'CHAIRMAN PARA', 'KHARULIA HINDO PARA', 'KHRULIA BAZAR', 'MOKBOL SOUDAGOR PARA', 'SIKDAR PARA', 'NOA PARA', 'TECCIFUL KONDAKAR PARA', 'TECCIFUL SIKDAR PARA', 'LOMBORI PARA',
'MONSI PARA', 'DENGA PARA', 'CAKMARKUL', 'KOLGOR BAZAR', 'MIAZI PARA', 'TECCIFUL STATION', 'RAMU BYPASS', 'FATEKARKUL', 'BONIK PARA STATION'];
echo json_encode($area);

