<?php
include 'config.php';
include 'layout/header.php';
if(isset($_SESSION['access_token'])){
    $url = $config['service'].'user';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization:Bearer {$_SESSION['access_token']}"]);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
?>
<ul>
    <li>
<b>        نام کابری:</b>
        <?=$_SESSION['username']?>
    </li>
    <li>
        <b>        همراه:</b>
        <?=$_SESSION['phone_number']?>
    </li>
</ul>
    <a class="btn btn-success" href="<?=$config['home']?>index.php">بازگشت</a>
<?php
}
else {
    echo "خطا";
}
include "layout/footer.php";
?>