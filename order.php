<?php
include "config.php";
include "layout/header.php";
include "utilities.php";
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=price'>برای خرید باید وارد شوید</a>";
}
else {
    $user = getUser();
    $userId = $user->userId;
    $bizId = getBusinessId();
    $curl = curl_init();
    $ott = getOtt();
    curl_setopt_array($curl, [
            //Attention: set pay parameter to true for decresing money from credit
        CURLOPT_URL => $config['service'] . "nzh/biz/issueInvoice?bizId={$bizId}&userId={$userId}&description=محصول&pay=true&postalCode=000000000&phoneNumber={$user->cellphoneNumber}&city=Tehrab&redirectUrl={$config['home']}&productId[]=0&price[]=5000&quantity[]=1&productDescription[]=services&state=test&address=test&deadline=1397/05/12&guildCode=INFORMATION_TECHNOLOGY_GUILD",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            '_token_: '.$config['api_token'],
            '_token_issuer_: 1',
            '_ott_: '.getOtt()
        ],
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $responseInvoice = curl_exec($curl);
    $err = curl_error($curl);
    $resp = json_decode($responseInvoice);
    $invoice_id = $resp->result->id;
    $_SESSION['invoice_id'] = $invoice_id;
    curl_close($curl);

    if ($err) {
        echo 'cURL Error #:' . $err;
    } else {
        $responseInvoice = json_decode($responseInvoice);
        $errMsg = false;
        if($responseInvoice->hasError){
            if($responseInvoice->errorCode==14){
                $errMsg = "این کاربر دنبال کننده کسب و کار نیست";
            }
            else if($responseInvoice->errorCode ==12){
                $errMsg = "موجودی کیف پول شما کافی نیست";
            }
            else {
                $errMsg = $responseInvoice->message;
            }
        }
        else {
            $errMsg = false;
        }

    }
    if($errMsg){
        ?>
        <div>
            <h2>خطا</h2>
            <div class="alert alert-danger"><?=$errMsg?></div>
            <a class="btn btn-success" href="<?=$config['home']?>index.php">بازگشت</a>
        </div>
        <?php
    }
    else {
        $redirect_uri = "{$config['home']}pay_return_redirect.php?fanapBillNumber=".$invoice_id;
        $call_uri = "{$config['home']}pay_return_redirect.php?fanapBillNumber=".$invoice_id;
        $url = 'http://sandbox.fanapium.com:1031/v1/pbc/payinvoice/?invoiceId='.
            $invoice_id . '&redirectUri='.$redirect_uri;
        header("Location: {$url}");
    }
}
include "layout/footer.php";
