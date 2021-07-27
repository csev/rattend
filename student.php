<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/namespaces/Tsugi.html

use \Tsugi\Util\U;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData(); 

// Model
$old_code = Settings::linkGet('code', '');

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

?>
<p><?= __("Enter code:") ?></p>
<form method="post">
    <input type="text" name="code" id="code" value="">
    <input type="submit" class="btn btn-normal" id="attend" name="set" value="<?= __('Record attendance') ?>"><br/>
</form>
<div id="alert"></div>
<?php

$OUTPUT->footerStart();

?>
<script>
$(document).ready(function(){
    $("#attend").click(function(e) {
        e.preventDefault();
        $("#alert").html("");
        var code = $("#code").val();
        $.ajax({
            type: "POST",
            url: '<?= U::addSession('api/attend.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ "code": code }),
            success: function (data) {
                console.log('Attend');
                if ( data.status == "success" ) {
                    $("#alert").html("Success");
                } else {
                    $("#alert").html("Incorrect code");
                }
            }
        });
    });
});
</script>
<?php
$OUTPUT->footerEnd();

