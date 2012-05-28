/* JavaScript includes for slam-laboratory

*/

function removeBodyId( id )
{
	var hDiv = document.getElementById( id );
	if (hDiv != null)
		hDiv.parentNode.removeChild(hDiv);
	return;
}

function hideBodyId( id )
{
	var hDiv = document.getElementById( id );
	hDiv.style.display='none';
	return;
}

function unhideBodyId( id )
{
	var hDiv = document.getElementById( id );
	hDiv.style.display='';
	return;
}

function toggleHideBodyId( id )
{
	var el = document.getElementById(id);

	if (el.style.display == 'none')
		el.style.display = '';
	else
		el.style.display = 'none';
	
	return;
}

function checkPasswordForm(formid, field1, field2)
{
	var form = document.getElementById(formid);
	var val1 = document.getElementById(field1).value;
	var val2 = document.getElementById(field2).value;

	if (val1 == val2){
		form.submit();
		return true;
	}
	
	alert('Passwords must match!');
	return false;
}

function doUserPreferencesProjectMenu( selection, id )
{
	el = document.getElementById( id );
	el.value = selection;

	if (selection == 'Other')
		el.style.display='';
	else
		el.style.display='none';
	
	return;
}

function clientSideInsertSrc(id, url)
{
	/* from http://www.boutell.com/newfaq/creating/include.html */
	
	var req = false;
	// For Safari, Firefox, and other non-MS browsers
	if (window.XMLHttpRequest)
	{
		try { req = new XMLHttpRequest(); }
		catch (e) { req = false; }
	}
	else if (window.ActiveXObject)
	{
		// For Internet Explorer on Windows
		try { req = new ActiveXObject("Msxml2.XMLHTTP"); }
		catch (e)
		{
			try { req = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch (e) { req = false; }
		}
	}

	var element = document.getElementById(id);
	if (!element) { return; }
	
	if (req)
	{
		// Synchronous request, wait till we have it all
		req.open('GET', url, false);
		req.send(null);
		element.innerHTML = req.responseText;
	}
	else
		element.innerHTML = "Sorry, your browser does not support XMLHTTPRequest objects.";
}
