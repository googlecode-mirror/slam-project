<?php
	set_include_path(get_include_path().PATH_SEPARATOR.'../');
	require('../lib/obj/slam_config.inc.php');
	$config = new SLAMconfig();
?>
<div style='text-align:center'>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>SLAM v.<?php echo($config->values['version']) ?></p>
		<a href='http://code.google.com/p/slam-project/' target='_new'>code.google.com/p/slam-project/</a>
	</div>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>Developed by:</p>
		<a href='http://elihuihms.com'>Elihu Ihms</a>
		<br />
		of
		<br />
		<a href='http://steelsnowflake.com'>SteelSnowflake LLC</a>
	</div>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>Special thanks:</p>
		The <a href='http://chemistry.osu.edu/~foster.281/' target='_new'>Mark Foster</a> lab
		<br />
		The <a href='http://www.osu.edu' target='_new'>Ohio State University</a>
	</div>
</div>
