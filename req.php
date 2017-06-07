<?php
include "config.php";
include "layout/header.php";
if(!isset($_SESSION['access_token'])){
    echo "<div class='alert alert-warning'>شما باید وارد شوید.\n\n</div>";
    echo "<br><a class='btn btn-warning' href='login.php?back=req'>برای درخواست پیک باید وارد شوید</a>";
}
else {
    ?>
    <div>
        <form method="post" action="price.php">
            <div class="form-group">
                <label for="from-input">مبدا</label>
                <input name="from"  required type="text" class="form-control autoSuggest"  id="from-input" autocomplete="off">
                <div class="suggest-box"></div>
                <input class="lat" required type="hidden" name="from-lat">
                <input class="lng" required type="hidden" name="from-lng">
            </div>
            <div class="form-group">
                <label for="to-input">مقصد</label>
                <input name="to" required type="text" class="form-control autoSuggest" id="to-input" autocomplete="off">
                <div class="suggest-box"></div>
                <input class="lat" required type="hidden" name="to-lat">
                <input class="lng" required type="hidden" name="to-lng">
            </div>
            <button type="submit" class="btn btn-default">استعلام قیمت</button>
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