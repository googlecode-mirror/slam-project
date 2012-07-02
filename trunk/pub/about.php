<?php
	set_include_path(get_include_path().PATH_SEPARATOR.'../');
	require('../lib/obj/slam_config.inc.php');
	$config = new SLAMconfig();
?>
<div style='text-align:center'>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>SLAM v.<?php echo($config->values['version']) ?> build <?php echo($config->values['build']) ?></p>
		<a href='http://code.google.com/p/slam-project/' target='_new'>code.google.com/p/slam-project/</a>
	</div>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>&copy; 2012 <a href='http://steelsnowflake.com'>SteelSnowflake LLC</a></p>
	</div>
	<div>
		<p style='font-weight:bold;margin-bottom:5px'>Special thanks:</p>
		The <a href='http://chemistry.osu.edu/~foster.281/' target='_new'>Mark Foster</a> lab
		<br />
		The <a href='http://www.osu.edu' target='_new'>Ohio State University</a>
	</div>
	<div>
		<p style='font-style:italic;margin-bottom:5px'>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
		<br />
		<br />
		This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the <a href='http://www.opensource.org/licenses/GPL-2.0'>GNU General Public License</a> for more details.</p>
	</div>	
</div>
