<?php

function pppAction($actionType, $pppName)
{
    $url = 'http://mt.baycombd.com/expnet_api/pppAction.php';
    $data = array(
        'ppp_name' => $pppName,
        'action_type' => $actionType
    );
    $postdata = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


$data = json_decode(pppAction('disable', 'saiful-31'), true);

if ($data['status'] == 200) {
    echo json_encode(array(
        "status" => 200,
        "message" => "OK"
    ));
}
