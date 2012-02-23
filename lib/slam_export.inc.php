<?php

set_include_path(get_include_path().PATH_SEPARATOR.'../');
set_include_path(get_include_path().PATH_SEPARATOR.'../lib');

/* class includes */
require('obj/slam_config.inc.php');
require('obj/slam_db.inc.php');
require('obj/slam_user.inc.php');
require('obj/slam_request.inc.php');
require('obj/slam_result.inc.php');

/* function includes */
require('logic/slam_functions.inc.php');
require('logic/slam_functions_db.inc.php');
require('logic/slam_functions_user.inc.php');
require('logic/slam_functions_asset.inc.php');
require('logic/slam_functions_search.inc.php');
require('logic/slam_functions_dash.inc.php');

?>
