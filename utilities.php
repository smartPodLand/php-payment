<?php


function getUser()
{
    global $config;
    $access_token = $_SESSION['access_token'];
    $ch = curl_init($config['service'].'nzh/getUserProfile/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "_token_: {$access_token}",
        "_token_issuer_: 1"
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        echo 'cURL Error #:' . $err;
        return false;
    } else {
        $resp = json_decode($response);
        return $resp->result;
    }
}
function getBusinessId(){
    global $config;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $config['service'] . "nzh/getUserBusiness",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            "_token_: {$config['api_token']}",
            "_token_issuer_: 1"
        ],
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return json_decode($response)->result->id;
    }

}

function getOtt(){
    global $config;
    $url =  $config['service'] . "nzh/ott/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "_token_: {$config['api_token']}",
        "_token_issuer_: 1"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $resp = json_decode($response);
    return $ott = $resp->ott;
}