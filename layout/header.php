<?php
session_start();
require "config.php";
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Fanap Peyk</title>

    <!-- Bootstrap core CSS -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">


    <!-- Custom styles for this template -->
    <link href="assets/main.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body dir="rtl"><div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-left">
                <?php
                if(isset($_SESSION['access_token'])){
                    ?>
                    <li><a href="logout.php">خروج</a></li>
                    <li><a href="user_info.php">اطلاعات کاربر</a></li>
                    <li><a href="req.php">درخواست پیک</a></li>
                    <?php
                }
                else {
                    ?>
                    <li><a href="login.php">ورود</a></li>
                    <li><a href="register.php">عضویت</a></li>
                    <li><a href="req.php">درخواست پیک</a></li>
                    <?php
                }
                ?>
            </ul>
        </nav>
        <?php

        //refresh token
        $isLoggedIn = false;
        if(isset($_SESSION['start_time'])) {
            $isLoggedIn = true;
            if ((time() - $_SESSION['start_time'])>$_SESSION['expires_in']) {
                //refreshing token:
                $url = $config['sso'] . 'token/';
                $ch = curl_init($url);
                $fields = "client_id={$config['client_id']}&client_secret={$config['client_secret']}&refresh_token={$_SESSION['refresh_token']}&redirect_uri={$config['home']}return.php&grant_type=refresh_token";
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                $response_header = curl_exec($ch);
                curl_close($ch);
                $token = json_decode($response_header);
                $_SESSION['access_token'] = $token->access_token;
                $_SESSION['refresh_token'] = $token->refresh_token;
                $_SESSION['expires_in'] = $token->expires_in;
                $_SESSION['start_time'] = time();
            }


            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $config['service'] . "nzh/getCredit",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST=>0,
                CURLOPT_SSL_VERIFYPEER=> 0,
                CURLOPT_HTTPHEADER => [
                    "_token_: {$_SESSION['access_token']}",
                    "_token_issuer_: 1"
                ],
            ]);
            $response_header = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                $response_header = false;
            } else {
                $response_header = json_decode($response_header);
                if(!$response_header->result){
                    $response_header->result[0]= new StdClass;
                    $response_header->result[0]->currencySrv= new StdClass;
                    $response_header->result[0]->amount = 0;
                    $response_header->result[0]->currencySrv->name = "ریال";
                }

            }
        }
        ?>
        <div class="pull-right">
        <h3 class="text-muted">فناپ پیک</h3>
            <?php if ($isLoggedIn) { ?>
            <div><b>اعتبار شما:</b><?=$response_header->result[0]->amount?><?=$response_header->result[0]->currencySrv->name?></div>
            <?php } ?>
        </div>
    </div>
    <?php
    ?>

