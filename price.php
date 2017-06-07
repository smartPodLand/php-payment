<?php
include "config.php";
include "layout/header.php";
//include "service/user_info.php";
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=req'>برای درخواست پیک باید وارد شوید</a>";
}
else {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $alopeyk['service']."orders/price/calc",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(
        [
            'coords' => [
                'from' => [
                    'lat' => $_POST['from-lat'],
                    'lng' => $_POST['from-lng'],
                ],
                'to' => [
                    'lat' => $_POST['to-lat'],
                    'lng' => $_POST['to-lng'],
                ],
            ],
            "has_return" => false,
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

    $time = $response->object->duration;
    $hours = floor($time / (60 * 60));
    $time -= $hours * (60 * 60);

    $minutes = floor($time / 60);
    $time -= $minutes * 60;

    $seconds = floor($time);
    $time -= $seconds;
    ?>

    <div>
        <div>
            <form method="post" action="order.php">

            <h2>هزینه</h2>
            <hr>
            <ul>
                <li><b>هزینه:</b> <?=$response->object->price?></li>
                <input  required type="hidden" name="price"  value="<?=$response->object->price?>">

                <li><b>فاصله:</b> <?=$response->object->distance?></li>
                <li><b>زمان:</b> <?=$hours.":".$minutes.":".$seconds?></li>
                <!--            <li><b>هزینه با برگشت:</b> //=$response->object->price_with_return?></li>-->
            </ul>
        </div>
        <h2>درخواست</h2>
        <hr>
            <div class="form-group">
                <label for="from-input">مبدا</label>
                <input  required type="hidden"  class="form-control autoSuggest"  id="from-input"  name="from-input"  value="<?=$_POST['from']?>">
                <input  required type="text"  class="form-control autoSuggest"  id="from-input"  name="from-input" disabled value="<?=$_POST['from']?>">
                <div class="suggest-box"></div>
                <input class="lat" required type="hidden" name="from-lat" value="<?=$_POST['from-lat']?>">
                <input class="lng" required type="hidden" name="from-lng" value="<?=$_POST['from-lng']?>">

            </div>
            <div class="form-group">
                <label for="from-number">پلاک</label>
                <input type="text" class="form-control" name="from-number">
            </div>
            <div class="form-group">
                <label for="from-unit">واحد</label>
                <input type="text" class="form-control" name="from-unit">
            </div>
            <div class="form-group">
                <label for="from-name">نام</label>
                <input type="text" class="form-control" name="from-fname">
            </div>
            <div class="form-group">
                <label for="from-name">نام خانوادگی</label>
                <input type="text" class="form-control" name="from-lname">
            </div>
            <div class="form-group">
                <label for="from-phone">تلفن همراه</label>
                <input type="text" class="form-control" name="from-phone" value="<?=$_SESSION['phone_number']?>">
            </div>
            <div class="form-group">
                <label for="from-description">توضیحات</label>
                <textarea class="form-control" name="from-description" id="" cols="30" rows="3"></textarea>
            </div>

            <hr>
            <div class="form-group">
                <label for="to-input">مقصد</label>
                <input required type="hidden" class="form-control autoSuggest" id="to-input" name="to-input"  value="<?=$_POST['to']?>">
                <input required type="text" class="form-control autoSuggest" id="to-input" name="to-input" disabled value="<?=$_POST['to']?>">
                <div class="suggest-box"></div>
                <input class="lat" required type="hidden" name="to-lat" value="<?=$_POST['to-lat']?>">
                <input class="lng" required type="hidden" name="to-lng" value="<?=$_POST['to-lng']?>">
            </div>
            <div class="form-group">
                <label for="to-number">پلاک</label>
                <input type="text" class="form-control" name="to-number">
            </div>
            <div class="form-group">
                <label for="to-unit">واحد</label>
                <input type="text" class="form-control" name="to-unit">
            </div>
            <div class="form-group">
                <label for="to-name">نام</label>
                <input type="text" class="form-control" name="to-fname">
            </div>
            <div class="form-group">
                <label for="from-name">نام خانوادگی</label>
                <input type="text" class="form-control" name="to-lname">
            </div>
            <div class="form-group">
                <label for="to-phone">تلفن همراه</label>
                <input type="text" class="form-control" name="to-phone" value="">
            </div>
            <div class="form-group">
                <label for="to-description">توضیحات</label>
                <textarea class="form-control" name="to-description" id="" cols="30" rows="3"></textarea>
            </div>

            <hr>
            <button type="submit" class="btn btn-default">پرداخت</button>
        </form>
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
                            $context.find('.lat').val(from.lat)
                            $context.find('.lng').val(from.lng)
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