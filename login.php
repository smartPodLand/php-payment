<?php
include 'config.php';
session_start();
if(isset($_SESSION['keylead_token'])){
    echo "شما قبلا وارد شده اید.";
}
else {
    if(isset($_GET['back'])){
        $_SESSION['back']=$_GET['back'];
    }
    header("Location: {$config['sso']}authorize/?client_id={$config['client_id']}&response_type=code&redirect_uri={$config['home']}return.php");
}