<?php
include "config.php";
include "layout/header.php";
//include "service/user_info.php";
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=price'>برای خرید باید وارد شوید</a>";
}
else {
    ?>

    <div>
        <div>
            <h2>کالا</h2>
            <hr>
            <ul>
                <li><b>هزینه:</b> 5000 ریال</li>
            </ul>
        </div>
        <a class="btn btn-success" href="order.php">پرداخت</a>
    </div>
    <?php
}
include "layout/footer.php";