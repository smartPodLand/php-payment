<?php
$order_id = $_GET['order_id'];
$curl = curl_init();
include '../config.php';
curl_setopt_array($curl, [
    CURLOPT_URL => $alopeyk['service'] . "orders/{$order_id}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $alopeyk['token'],
        'X-Requested-With: XMLHttpRequest'
    ],
]);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo json_encode(['status'=>'error']);
} else {
    echo $response;
}
