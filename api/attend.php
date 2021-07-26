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
$retval = new \stdClass();
$retval->status = "failure";

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

