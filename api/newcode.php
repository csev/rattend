<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\Output;
use \Tsugi\Util\U;
use \Tsugi\Util\Net;

// Make sure errors are sent via JSON
Output::headerJson();

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData(); 

// Takes raw data from the request
$json = file_get_contents('php://input');
$data = json_decode($json);

// Model
$p = $CFG->dbprefix;
$old_code = Settings::linkGet('code', '');

if ( isset($data->code) && $LAUNCH->user->instructor ) {
    Settings::linkSet('code', $data->code);
} else {
    Net::send403();
    return;
}

$retval = new \stdClass();
$retval->status = "success";

Output::jsonOutput($retval);
