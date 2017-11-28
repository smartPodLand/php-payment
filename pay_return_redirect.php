<?php
include "layout/header.php";
$errMsg = false;
$noLogin = false;
if(!isset($_SESSION['access_token'])){
    $errMsg = "شما وارد وبسایت نشده اید لطفا از گزینه ورود برای وارد شدن به وبسایت استفاده کنید.";
    $noLogin = true;
}
else {
    $invoice_id_enter = $_SESSION['invoice_id'];
    $api_token = $config['api_token'];

    //verify verificationNeeded=true invoice
    $url = $config['service'].'nzh/biz/verifyInvoice';
    $ch = curl_init($url);
    $fields = "id=".$invoice_id_enter;

    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                '_token_: '.$config['api_token'],
                '_token_issuer_: 1',
            ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response_verify = curl_exec($ch);

    //verify invoice is paid
    $url = "http://sandbox.fanapium.com:8080/nzh/biz/getInvoiceList/?size=1&id={$_GET['fanapBillNumber']}&firstId=0";
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
    $is_payed = $resp->result[0]->payed;

    $is_canceled = $resp->result[0]->canceled;
    $invoice_id = $_GET['fanapBillNumber'];
    $responseInvoice = $resp;

    if ($err) {
        echo 'cURL Error #:' . $err;
    } else {
        if(isset($responseInvoice->hasError) & $responseInvoice->hasError){
            $errMsg = $responseInvoice->message." ".$responseInvoice->errorCode;
        }
        else {
            $invoice = $responseInvoice->result[0];
            if($invoice->payed){
                ?>
                <div>
                    <h2>وضعیت</h2>
                    <hr>
                    <div class="text-center"><b>وضعیت: </b><span class="blink_me" id="status">خرید با موفقیت انجام شد.</span></div>
                    <br>
                <?php
            }
            else {
                $errMsg ="خطا در پرداخت فاکتور";
            }
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