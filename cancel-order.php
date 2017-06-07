<?php
include "config.php";
include "layout/header.php";
$order_id = $_GET['order_id'];
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=req'>برای درخواست پیک باید وارد شوید</a>";
}
else {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $alopeyk['service'] . "orders/{$order_id}/cancel",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $alopeyk['token'],
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
        <div class="text-center"><b>وضعیت: </b><span id="status">کنسل</span></div>
        <br>
        <br>
        <img src="<?=$response->object->screenshot->url?>" alt="">
        <br>
        <br>
        <div class="order-buttons text-center">
            <button class="btn btn-primary" disabled>دریافت آخرین وضعیت</button>
            <button class="btn btn-warning" disabled>لغو پیک</button>
            <br>
            <a href="req.php" class="btn btn-success rereq">درخواست پیک دیگر</a>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script>
        $(document).ready(function () {
            var from = {};
            var to = {};
            $(".autoSuggest").keyup(function () {
                $this = $(this);
                $.get('service/suggestAddress.php',{'location':$this.val()},function (data) {
                    console.log(JSON.parse(data));
                    var result = JSON.parse(data);
                    if (result.status == 'success') {
                        var list = result.object;

                        var box = "<div><ul>";
                        for (var i = 0; i < list.length; i++) {
                            box += "<li class='location-li' dataLocation=\'"+JSON.stringify(list[i])+"\'>" +
                                "<b>" + list[i].title + "</b><br>" +
                                "<i>" + list[i].region + "</i><br>" +
                                "<small>" + list[i].district + "</small><br>"
                        }
                        box += "</ul></div>";
                        $this.next('.suggest-box').html(box);
                        $(".location-li").click(function () {
                            console.log("dataLocation",$(this).attr('dataLocation'));
                            from = JSON.parse($(this).attr('dataLocation'));
                            var inputText = from['title']+" - "+ from['region']+" - "+ from['district'];
                            var $context = $(this).parent().parent().parent().parent();
                            $context.find('.autoSuggest').val(inputText);
                            $context.find('.autoSuggest').attr('data',$(this).attr('dataLocation'));
                            $context.find('.lat').val(from.lat);
                            $context.find('.lng').val(from.lng);
                            $('.suggest-box').html("");

                        })
                    }
                    else {
                        console.error("خطا");
                    }
                });


            })
        });
    </script>
<?php
}
include "layout/footer.php";
