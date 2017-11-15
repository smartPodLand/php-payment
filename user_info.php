<?php
include 'config.php';
include 'utilities.php';
include 'layout/header.php';
if(isset($_SESSION['access_token'])){
   $user = getUser();
?>
<ul>
    <li>
<b>        نام کابری:</b>
        <?=$user->username?>
    </li>
    <li>
        <b>        همراه:</b>
        <?=$user->cellphoneNumber?>
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