<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/namespaces/Tsugi.html

use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData(); 

// Model
$p = $CFG->dbprefix;
$old_code = Settings::linkGet('code', '');

// View
$menu = false;
if ( $USER->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    $menu->addRight(__('Settings'), '#', /* push */ false, SettingsForm::attr());
}

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();

if ( $USER->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    $menu->addRight(__('Settings'), '#', /* push */ false, SettingsForm::attr());
}
$OUTPUT->topNav($menu);

if ( $USER->instructor ) {
    echo('<div style="float:right;">');
    echo('<form method="post" style="display: inline">');
    echo('<input type="submit" class="btn btn-warning" id="clear" name="clear" value="'.__('Clear data').'"><br/>'."\n");
    echo("</form>\n");
    echo('</div>');

    $OUTPUT->welcomeUserCourse();
    echo('<br clear="all">');
    SettingsForm::start();
    echo("<p>Configure the LTI Tool<p>\n");
    SettingsForm::text('code',__('Code'));
    SettingsForm::checkbox('grade',__('Send a grade'));
    SettingsForm::text('match',__('Limit access by IP address.  This can be a prefix of an IP address like "142.16.41" or if it starts with a "/" it can be a regular expression (PHP syntax)'));
    echo("<p>Your current IP address is ".htmlentities(Net::getIP())."</p>\n");
    SettingsForm::done();
    SettingsForm::end();
}

?>
<!-- Pre-React version of the tool -->
<div id="alert"></div>
<form method="post">
<p><?= __("Enter code:") ?></p>
<?php if ( $LAUNCH->user->instructor ) { ?>
<input type="text" name="code" id="code" value="<?= $old_code ?>">
<input type="submit" class="btn btn-normal" id="set" name="set" value="<?= __('Update code') ?>">
</form>
<div id="attend-div"><img src="<?= $OUTPUT->getSpinnerUrl() ?>"></div>
<?php } else { ?>
    <input type="text" name="code" id="code" value="">
    <input type="submit" class="btn btn-normal" id="attend" name="set" value="<?= __('Record attendance') ?>"><br/>
</form>
<?php } ?>
<?php

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('attend'));

?>
<script>
$(document).ready(function(){
<?php if ( $LAUNCH->user->instructor ) { ?>
    $.getJSON('<?= addSession('api/getrows.php') ?>', function(rows) {
        window.console && console.log(rows);
        context = { 'rows' : rows,
            'instructor' : true,
            'old_code' : '<?= $old_code ?>'
        };
        tsugiHandlebarsToDiv('attend-div', 'attend', context);
    }).fail( function() { alert('getJSON fail'); } );

    $("#clear").click(function(e) {
        e.preventDefault();
        var code = $("#code").val();
        $.ajax({
            type: "POST",
            url: '<?= addSession('api/clear.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ "code": code }),
            success: function (data) {
                console.log('Data', data);
                alert("clear "+code);
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
            data: JSON.stringify({ "code": code }),
            success: function (data) {
                console.log('Data', data);
                alert("Set "+code);
            }
        }).done(function(data) {
                console.log('Data', data);
                alert("Set "+code);
        });
    });
    $(document).ready(function(){
        tsugiHandlebarsToDiv('attend-div', 'attend', {});
    });
<?php } else { ?>
    $("#attend").click(function(e) {
        e.preventDefault();
        var code = $("#code").val();
        $.ajax({
            type: "POST",
            url: '<?= addSession('api/attend.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ "code": code }),
            success: function(data) { 
                console.log('Data', data);
                alert("Attend "+data.status);
            }
        });
    });
<?php } ?>

});
</script>
<?php
$OUTPUT->footerEnd();

