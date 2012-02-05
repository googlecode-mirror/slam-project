<?php

set_include_path(get_include_path().PATH_SEPARATOR.'../');
set_include_path(get_include_path().PATH_SEPARATOR.'../lib');

require('obj/slam_config.inc.php');
require('obj/slam_db.inc.php');
require('obj/slam_user.inc.php');
require('obj/slam_module.inc.php');
require('obj/slam_request.inc.php');
require('obj/slam_result.inc.php');

require('logic/slam_functions.inc.php');
require('logic/slam_functions_db.inc.php');
require('logic/slam_functions_asset.inc.php');
require('logic/slam_functions_files.inc.php');
require('logic/slam_functions_module.inc.php');

require('html/slam_html_files.inc.php');

?>