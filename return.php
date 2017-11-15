<?php
include 'config.php';
include 'utilities.php';
session_start();
$_SESSION['keylead_code']=$_GET['code'];

$url = $config['sso'].'token/';
$ch = curl_init($url);
$fields = "client_id={$config['client_id']}&client_secret={$config['client_secret']}&code={$_GET['code']}&redirect_uri={$config['home']}return.php&grant_type=authorization_code";

curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
$token = json_decode($response);

$_SESSION['access_token']= $token->access_token;
$_SESSION['refresh_token']= $token->refresh_token;
$_SESSION['expires_in']= $token->expires_in;
$_SESSION['start_time']= time();
$_SESSION['username'] = getUser()->name;
$_SESSION['phone_number'] = getUser()->cellphoneNumber;
$_SESSION['email'] = getUser()->email;

$back = 'index';
if(isset($_SESSION['back']) and $_SESSION){
    $back = $_SESSION['back'];
}

$responseFollow = followBusiness($_SESSION['access_token']);
if($responseFollow->hasError){
    echo " ERROR follow bussines";
}
else {
    header("Location: {$config['home']}{$back}.php");
}

function followBusiness($access_token){
    global $config;
    $businessId = getBusinessId();
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $config['service'] . "nzh/follow/?businessId={$businessId}&follow=true",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            "_token_: {$access_token}",
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
        return json_decode($response);
    }
}
