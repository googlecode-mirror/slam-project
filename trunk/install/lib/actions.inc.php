<?php

function write_SLAM_options( $filename )
{
	#
	# writes all options starting with "SLAM_" to a specified filename
	#
	
	$output = '';
	foreach( $_REQUEST  as $key=>$value )
		if( strpos($key, 'SLAM_') === 0 )
			$output .= "$key=\"$value\"\n";
	
	if( file_put_contents( $filename, $output) === false)
		return "Could not write option file.";
	
	return true;
}

function write_SLAM_config( )
{
	#
	# converts all the options from the individual step files to the slam config file
	#
	
	$ret = array();
	
	if( ($step_1_ini = parse_ini_file('./step_1.ini')) == false)
		$ret[] =  "Could not read option file from step 1";
	if( ($step_2_ini = parse_ini_file('./step_2.ini')) == false)
		$ret[] =  "Could not read option file from step 2";
	if( ($step_3_ini = parse_ini_file('./step_3.ini')) == false)
		$ret[] = "Could not read option file from step 3";
	
	if( count($ret) > 0 )
		return $ret;
	
	$opt_array = array_merge( $step_1_ini, $step_2_ini, $step_3_ini );
	
	if( ($config_ini = file_get_contents( './configuration.ini' )) == false)
		$ret[] = "Could not read configuration file template.";
	if( ($prefs_ini = file_get_contents( './preferences.ini' )) == false)
		$ret[] = "Could not read preference file template.";
	
	if( count($ret) > 0 )
		return $ret;
	
	# realpath() and trim() replacements
	$opt_array['SLAM_CONF_PATH'] = realpath( trim($opt_array['SLAM_CONF_PATH'],'/'));
	$opt_array['SLAM_FILE_ARCH_DIR'] = realpath( trim($opt_array['SLAM_FILE_ARCH_DIR'],'/'));
	$opt_array['SLAM_FILE_TEMP_DIR'] = realpath( trim($opt_array['SLAM_FILE_TEMP_DIR'],'/'));
	
	# write all of the simple replacements
	foreach( $opt_array as $key=>$value )
	{
		$config_ini = str_replace( $key, $value, $config_ini );
		$prefs_ini = str_replace( $key, $value, $prefs_ini );
	}
	
	# get installation path
	$path = $opt_array['SLAM_CONF_PATH'];
	
	print ">$config_ini<\n";
	print ">$prefs_ini<\n";
	
#	if( file_put_contents( "$path/configuration.ini", $config_ini) === false )
#		return "Could not write configuration file.";
#	if( file_put_contents( "$path/preferences.ini", $prefs_ini) === false)
#		return "Could not write preferences file.";
	
	return true;
}

?>
