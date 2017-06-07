<?php
$curl = curl_init();
require '../config.php';
$url = $alopeyk['service']."locations?input=".$_GET['location'];
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
//    CURLOPT_POSTFIELDS => json_encode(
//        array(
//            'coords' => array(
//                'from' => array(
//                    'lat' => 35.755460,
//                    'lng' => 51.416874,
//                ),
//                'to' => array(
//                    'lat' => 35.758495,
//                    'lng' => 51.442550,
//                ),
//            ),
//            "has_return" => false,
//        )
//    ),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $alopeyk['token'],
        //  'Content-Type: application/json; charset=utf-8',
        'X-Requested-With: XMLHttpRequest'
    ],
]);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo 'cURL Error #:' . $err;
} else {
    echo $response;
}
