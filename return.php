<?php
include 'config.php';
session_start();
$_SESSION['keylead_code']=$_GET['code'];

$url = $config['sso'].'token/';
$ch = curl_init($url);
$fields = "client_id={$config['client_id']}&client_secret={$config['client_secret']}&code={$_GET['code']}&redirect_uri={$config['home']}return.php&grant_type=authorization_code";
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$response = curl_exec($ch);
curl_close($ch);
$token = json_decode($response);
echo "<pre>";
print_r($token);
echo "</pre><br>";
$_SESSION['access_token']= $token->access_token;
$_SESSION['refresh_token']= $token->refresh_token;
$_SESSION['expires_in']= $token->expires_in;
$_SESSION['start_time']= time();
$_SESSION['username'] = getUser($token->access_token)->preferred_username;
$_SESSION['phone_number'] = getUser($token->access_token)->phone_number;

$r = registerWithSSO($_SESSION['access_token'], $_SESSION['username']);
print_r($r);
if($r->hasError){
    echo  "ERROR";
}
else {
    $back = 'index';
    if(isset($_SESSION['back']) and $_SESSION){
        $back = $_SESSION['back'];
    }
    $rfb = followBusiness($_SESSION['access_token']);
    if($rfb->hasError){
        echo "ERROR";
    }
    else {
        header("Location: {$config['home']}{$back}.php");
    }
}



function registerWithSSO($access_token,$nickname){
    global $config;
    $curl = curl_init();
    echo $config['service'] . "aut/registerWithSSO/?nickname=\"{$nickname}\"";
    curl_setopt_array($curl, [
        CURLOPT_URL => $config['service'] . "aut/registerWithSSO/?nickname=\"{$nickname}\"",
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
function getBusinessId(){
    global $config;
    $curl = curl_init();
    //echo $config['service'] . "aut/registerWithSSO/?nickname=\"{$nickname}\"";
    curl_setopt_array($curl, [
        CURLOPT_URL => $config['service'] . "nzh/getUserBusiness",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            "_token_: {$config['business_token']}",
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
function followBusiness($access_token){
    global $config;
    $businessId = getBusinessId();
    $curl = curl_init();
    echo $config['service'] . "nzh/follow/?businessId={$businessId}&follow=true";
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
function getUser($access_token){
    global $config;
    $url = $config['sso_service'].'user';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization:Bearer {$access_token}"]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
}
