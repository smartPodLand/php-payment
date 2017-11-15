<?php
include "layout/header.php";
$errMsg = false;
$noLogin = false;
if(!isset($_SESSION['access_token'])){
    $errMsg = "شما وارد وبسایت نشده اید لطفا از گزینه ورود برای وارد شدن به وبسایت استفاده کنید.";
    $noLogin = true;
}
else {
    ?>
                <div>
                    <h2>وضعیت</h2>
                    <hr>
                    <div class="text-center"><b>برای ابطال یا بستن آخرین فاکتور صادر شده از دکمه های زیر استفاده کنید.</b></div>
                    <div class="text-center"><i>در شرایط واقعی این گزینه ها در دسترس ادمین باید باشد.</i></div>
                    <br>
                    <div class="text-center">
                        <a class="btn btn-warning" href="cancel_invoice.php">ابطال فاکتور</a>
                        <a class="btn btn-danger" href="close_invoice.php">بستن فاکتور</a>
                    </div>

                <?php

}
if($errMsg) {
    ?>
    <div class='alert alert-warning'><?= $errMsg ?></div>
    <br><a class='btn btn-success' href='admin-index.php'>بازگشت</a>
    <?=$noLogin?"<a class='btn btn-primary' href='login.php?back=price'>ورود</a>":''?>
    <?php
    include 'layout/footer.php';
}