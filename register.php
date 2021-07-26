<?php

$REGISTER_LTI2 = array(
"name" => "Attendance Tool",
"FontAwesome" => "fa-server",
"short_name" => "Attendance Tool",
"description" => "This is an experimental version of the assignments tool to explore React support for Tsugi.",
    // By default, accept launch messages..
    "messages" => array("launch"),
    "tool_phase" => "react",
    "privacy_level" => "name_only",  // anonymous, name_only, public
    "license" => "Apache",
    "languages" => array(
        "English", "Spanish"
    ),
    "source_url" => "https://github.com/csev/rattend",
    // For now Tsugi tools delegate this to /lti/store
    "placements" => array(
        /*
        "course_navigation", "homework_submission",
        "course_home_submission", "editor_button",
        "link_selection", "migration_selection", "resource_selection",
        "tool_configuration", "user_navigation"
        */
    ),
    "screen_shots" => array(
        "store/screen-01.png",
        "store/screen-02.png",
        "store/screen-03.png"
    )

);
