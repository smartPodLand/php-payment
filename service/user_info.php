<?php
if(isset($_SESSION['access_token'])) {
    $url = $config['service'] . 'user';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization:Bearer {$_SESSION['access_token']}"]);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
    $from_phone = $result->phone_number;
}
