<?php
if (file_exists('../documents/' . '652f6f182c034.jpeg')) {
    echo json_encode(array(
        "status" => 200,
        "message" => "Exist"
    ));
    //$client->document = $data->document;
    // if ($client->client_details_update()) {
    //     echo json_encode(array(
    //         "status" => 200,
    //         "message" => "Registration update Success"
    //     ));
    // }
}

?>