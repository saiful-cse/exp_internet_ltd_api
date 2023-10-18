<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

$area= ['---', 'DOKKIN PATOLI', 'PATOLI MIAZI PARA', 'UTTOR PATOLI', 'MASTER PARA', 'MASTER BARI', 'GAT PARA', 'MOG PARA', 'CHAIRMAN PARA', 'KHARULIA HINDO PARA', 'KHRULIA BAZAR', 'MOKBOL SOUDAGOR PARA', 'SIKDAR PARA ROAD', 'MEHER ALI PARA ROAD', 'NOA PARA', 'KONAR PARA', 'VOOT PARA', 'TECCIFUL UTTOR SIDE', 'TECCIFUL DOKKIN SIDE',
'MONSI PARA', 'DENGA PARA', 'CAKMARKUL', 'SHAHMODER PARA', 'JARAILTOLI', 'KOLGOR BAZAR', 'KOLGOR MIAZI PARA', 'TECCIFUL STATION', 'RAMU BYPASS', 'FATEKARKUL', 'BONIK PARA STATION'];
echo json_encode($area);

$query = "";

