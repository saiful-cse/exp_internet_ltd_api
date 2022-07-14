<?php
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(array(
        "status" => 416,
        "message" => "Unauthorized Access!!"
    ));

?>