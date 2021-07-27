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

if ( ! $USER->instructor ) {
    header( 'Location: '.U::addSession('student.php') ) ;
    return;
}

// If settings were updated
if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.U::addSession('index.php') ) ;
    return;
}

// Model
$p = $CFG->dbprefix;
$old_code = Settings::linkGet('code', '');

// View
$menu = false;
$menu = new \Tsugi\UI\MenuSet();
$menu->addRight(__('Settings'), '#', /* push */ false, SettingsForm::attr());

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);

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
// SettingsForm::end(false);
SettingsForm::end(true); // Use API

?>
<div id="alert"></div>
<div id="attend-div"><img src="<?= $OUTPUT->getSpinnerUrl() ?>"></div>
<?php

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('attend'));

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

    $("#clear").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '<?= U::addSession('api/clear.php') ?>',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({}),
            success: function (data) {
                location.reload();
            }
        });
    });
    $(document).ready(function(){
        tsugiHandlebarsToDiv('attend-div', 'attend', {});
    });
});
</script>
<?php
$OUTPUT->footerEnd();

