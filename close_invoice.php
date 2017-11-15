<?php
include "layout/header.php";
$errMsg = false;
$noLogin = false;
if(!isset($_SESSION['access_token'])){
    $errMsg = "شما وارد وبسایت نشده اید لطفا از گزینه ورود برای وارد شدن به وبسایت استفاده کنید.";
    $noLogin = true;
}
else {
    $invoice = $_SESSION['invoice_id'];
    $api_token = $config['api_token'];
    $url = $config['service']."nzh/biz/closeInvoice/?id={$invoice}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "_token_: {$api_token}",
        "_token_issuer_: 1"
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    $resp = json_decode($response);
    if ($err) {
        echo 'cURL Error #:' . $err;
    } else {
        if(isset($resp->hasError) & $resp->hasError){
            $errMsg = $resp->message." ".$resp->errorCode;
        }
        else { ?>
                <div>
                    <h2>وضعیت</h2>
                    <hr>
                    <div class="text-center"><b>وضعیت: </b><span class="blink_me" id="status">فاکتور با موفقیت بسته شد.</span></div>
                    <br>
                    <div class="text-center">
                        <a class="btn btn-success" href="price.php">خرید دیگر</a>
                    </div>
            <?php
        }
    }
}
if($errMsg) {
    ?>
    <div class='alert alert-warning'><?= $errMsg ?></div>
    <br><a class='btn btn-success' href='index.php'>بازگشت</a>
    <?=$noLogin?"<a class='btn btn-primary' href='login.php?back=price'>ورود</a>":''?>
    <?php
    include 'layout/footer.php';
}