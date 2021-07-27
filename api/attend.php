<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;
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
$old_code = $LAUNCH->link->settingsGet('code');
$send_grade = $LAUNCH->link->settingsGet('grade');
$match = $LAUNCH->link->settingsGet('match');
$ip = Net::getIP();

$retval = new \stdClass();
$retval->status = "failure";
$retval->detail = "";

if ( strlen($match) > 0 && substr($match, 0, 1) == '/' ) {
    if (!preg_match($match, $ip) ) {
        $retval->detail = __('IP Address '.$ip.' does not match (regex).');
        $retval->status = "failure";
        Net::send403();
        Output::jsonOutput($retval);
        return;
    }
}

if ( strlen($match) > 0 && substr($match, 0, 1) != '/' ) {
    if ( strpos($match, $ip) === false ) {
        $retval->detail = __('IP Address '.$ip.' does not match.');
        $retval->status = "failure";
        Net::send403();
        Output::jsonOutput($retval);
        return;
    }
}

if ( $old_code == $data->code ) {
    $PDOX->queryDie("INSERT INTO {$p}attend
        (link_id, user_id, ipaddr, attend, updated_at)
        VALUES ( :LI, :UI, :IP, NOW(), NOW() )
        ON DUPLICATE KEY UPDATE updated_at = NOW()",
        array(
            ':LI' => $LINK->id,
            ':UI' => $USER->id,
            ':IP' => Net::getIP()
        )
    );
    $retval->status = "success";
}

Output::jsonOutput($retval);

