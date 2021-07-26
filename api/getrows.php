<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Util\Net;

$LAUNCH = LTIX::requireData(); 
if ( ! $USER->instructor ) {
    $OUTPUT->jsonError('not authorized');
    return;
}

$rows = $PDOX->allRowsDie("SELECT A.user_id,attend,A.ipaddr, displayname, email
            FROM {$CFG->dbprefix}attend AS A
            JOIN {$CFG->dbprefix}lti_user AS U ON U.user_id = A.user_id
            WHERE link_id = :LI ORDER BY attend DESC, user_id",
     array(':LI' => $LINK->id)
);

foreach($rows as $row) {
    $displayname = $row['user_id'];
    if ( strlen($row['email']) > 0 ) {
        $displayname .= ' | ';
        $displayname .= $row['email'] ;
    }
    if ( strlen($row['displayname']) > 0 ) {
        $displayname .= ' | ';
        $displayname .= $row['displayname'] ;
    }
    $row['displayname'] = $displayname;
}

$OUTPUT->jsonOutput($rows);

