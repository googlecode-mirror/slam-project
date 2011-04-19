<?php

function SLAM_getDashboardResult($config,$db,$user,$request)
{
	/* make a request for the user's tagged identifiers */
	$dash_req = new SLAMrequest;
	$dash_req->categories = $user->prefs['identifiers'];
	
	/* preserve the current ordering scheme */
	$dash_req->order = $request->order;

	/* retrieve the results */
	return new SLAMresult($config,$db,$user,$dash_req);
}

?>