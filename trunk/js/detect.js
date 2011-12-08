function checkBrowser()
{
	/*
	 *this function returns an integer classifying the state of the browser compatibility
	 *
	 * 0 - javascript fail
	 * 1 - cookie fail
	 * 2 - OK
	*/
	
	/* set up failback function */
	window.onError = function()
	{
		return;
	}
	
	/* check for javascript object awareness */
	if (Object.create)
	{
		/* check for javascript error catching */
		try
		{
			/* check for enabled cookies */
			if( navigator.cookieEnabled )
				return 2;
			else
				return 1;
		}
		catch (errors)
		{
			return 0;
		}
	}
	return 0;
}