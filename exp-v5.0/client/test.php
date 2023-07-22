<?php

$url = 'http://103.134.39.238/pppCreate.php';
            $data = array(
                'ppp_name' => 'ss-expnet-300',
                'ppp_pass' => '89919161',
                'pkg_id' => '101',
                'mode' => 'Disable'
            );
            $postdata = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $result = curl_exec($ch);
            curl_close($ch);
            
            $api_response = json_decode($result);
            echo json_encode($api_response);