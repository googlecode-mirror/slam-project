<?php

function SLAM_getDashboardResult(&$config,$db,$user,$request)
{
	/* make a request for the user's tagged identifiers */
	$dash_req = new SLAMrequest;
	$dash_req->categories = $user->prefs['identifiers'];
	
	/* no tagged assets? bail */
	if( !is_array($dash_req) )
		return new SLAMresult($config,$db,$user,array());
	
	/* preserve the current ordering scheme */
	$dash_req->order = $request->order;
	
	/* check the user's categories against the available categories */
	$available_categories = array_keys($config->categories);
	
	foreach($dash_req->categories as $category=>$foo){
		if(!in_array($category,$available_categories)){
			$config->errors[] = "User has tagged assets present in unavailable category \"$category\".";
			unset($dash_req->categories[$category]);
		}
	}
	
	/* retrieve the results */
	return new SLAMresult($config,$db,$user,$dash_req);
}

?>