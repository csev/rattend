<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/namespaces/Tsugi.html

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData(); 

// Model
$p = $CFG->dbprefix;
$old_code = Settings::linkGet('code', '');

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();
$OUTPUT->welcomeUserCourse();

?>
<!-- Pre-React version of the tool -->
<form method="post">
<p><?= __("Enter code:") ?></p>
<?php if ( $LAUNCH->user->instructor ) { ?>
<input type="text" name="code" id="code" value="<?= $old_code ?>">
    <input type="submit" class="btn btn-normal" id="set" name="set" value="<?= __('Update code') ?>">
    <input type="submit" class="btn btn-warning" id="clear" name="clear" value="<?= __('Clear data') ?>"><br/>
<?php } else { ?>
    <input type="text" name="code" id="code" value="">
    <input type="submit" class="btn btn-normal" id="attend" name="set" value="<?= __('Record attendance') ?>"><br/>
<?php } ?>
</form>
<div id="alert"></div>
<div id="attend-div"><img src="<?= $OUTPUT->getSpinnerUrl() ?>"></div>
<?php

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('attend'));

if ( $LAUNCH->user->instructor ) {
?>
<script>
$(document).ready(function(){
    $.getJSON('<?= addSession('api/getrows.php') ?>', function(rows) {
        window.console && console.log(rows);
        context = { 'rows' : rows,
            'instructor' : true,
            'old_code' : '<?= $old_code ?>'
        };
        tsugiHandlebarsToDiv('attend-div', 'attend', context);
    }).fail( function() { alert('getJSON fail'); } );

<?php if ( $LAUNCH->user->instructor ) { ?>
    $("#clear").click(function(e) {
        e.preventDefault();
        var code = $("#code").val();
        $.ajax({
            type: "POST",
            url: '<?= addSession('api/clear.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            async: false,
            data: JSON.stringify({ "code": code }),
            success: function () {
                alert("Thanks!");
            }
        });
    });
    $("#set").click(function(e) {
        e.preventDefault();
        var code = $("#code").val();
        $.ajax({
            type: "POST",
            url: '<?= addSession('api/newcode.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            async: false,
            data: JSON.stringify({ "code": code }),
            success: function () {
                alert("Thanks!");
            }
        });
    });
<?php } else { ?>
    $("#attend").click(function(e) {
        e.preventDefault();
        var code = $("#code").val();
        alert("Attend "+code);
    });
<?php } ?>

});
</script>
<?php } else { ?>
<script>
$(document).ready(function(){
    tsugiHandlebarsToDiv('attend-div', 'attend', {});
});
</script>
<?php
} // End $LAUNCH->user->instructor
$OUTPUT->footerEnd();

