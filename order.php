<?php
include "config.php";
include "layout/header.php";
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=req'>برای درخواست پیک باید وارد شوید</a>";
}
else {
    $curl = curl_init();
    curl_setopt_array($curl, [
            //Attention: set pay parameter to true for decresing money from credit
        CURLOPT_URL => $config['service'] . "nzh/biz/issueInvoice?bizId=23&userId=108&description=پیک&pay=true&postalCode=000000000&phoneNumber={$_POST['from-phone']}&city=dsds&redirectUrl={$config['home']}&productId[]=0&price[]={$_POST['price']}&quantity[]=1&productDescription[]=services&state=test&address=test&deadline=1397/05/12&guildCode=INFORMATION_TECHNOLOGY_GUILD",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            '_token_: '.$config['business_token'],
            '_token_issuer_: 1',
            'X-Requested-With: XMLHttpRequest'
        ],
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $responseInvoice = curl_exec($curl);
    $err = curl_error($curl);

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
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $alopeyk['service'] . "orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                [
                    'from_lat' => $_POST['from-lat'],
                    'from_lng' => $_POST['from-lng'],
                    'to_lat' => $_POST['to-lat'],
                    'to_lng' => $_POST['to-lng'],
                    'from_info' => [
                        'address' => $_POST['from-input'],
                        'unit' => $_POST['from-unit'],
                        'number' => $_POST['from-number'],
                        'description' => $_POST['from-description'],
                        'sender' => [
                            'firstname' => $_POST['from-fname'],
                            'lastname' => $_POST['from-lname'],
                            'phone' => $_POST['from-fname']
                        ]
                    ],
                    'to_info' => [
                        'address' => $_POST['to-input'],
                        'unit' => $_POST['to-unit'],
                        'number' => $_POST['to-number'],
                        'description' => $_POST['to-description'],
                        'sender' => [
                            'firstname' => $_POST['to-fname'],
                            'lastname' => $_POST['to-lname'],
                            'phone' => $_POST['to-fname']
                        ]
                    ]
                ]
            ),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $alopeyk['token'],
                'Content-Type: application/json; charset=utf-8',
                'X-Requested-With: XMLHttpRequest'
            ],
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            $response = json_decode($response);
        }
        ?>
        <div>
            <h2>وضعیت</h2>
            <hr>
            <div class="text-center"><b>وضعیت: </b><span class="blink_me" id="status">شروع</span></div>
            <br>
            <br>
            <img src="<?= $response->object->screenshot->url ?>" alt="">
            <input type="hidden" id="order_id" value="<?= $response->object->id ?>">
            <br>
            <br>
            <div class="order-buttons text-center">
                <button id="get-status" class="btn btn-primary">دریافت آخرین وضعیت</button>
                <a class="btn btn-warning" href="cancel-order.php?order_id=<?= $response->object->id ?>">لغو پیک</a>
                <br>
                <a href="req.php" class="btn btn-success rereq">درخواست پیک دیگر</a>
            </div>
        </div>
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#get-status").click(function () {
                    $this = $(this);
                    $("#status").addClass('blink_me');
                    $.get('service/status.php', {'order_id': $('#order_id').val()}, function (data) {
                        var result = JSON.parse(data);
                        if (result.status == 'success') {
                            $("#status").removeClass('blink_me');
                            $("#status").text(JSON.parse(data).object.status)
                        }
                        else {
                            console.error("خطا");
                        }
                    });
                });
                setTimeout(function () {
                    $("#status").removeClass('blink_me');
                }, 50)
            });
        </script>
        <?php
    }
}
include "layout/footer.php";
